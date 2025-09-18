<?php

// ambil TokoElektronik
require_once 'TokoElektronik.php';

// buat folder uploads jika tidak ada
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// bikin session
session_start();

// bikin data sample make session
if (!isset($_SESSION['dataBarang'])) {
    $_SESSION['dataBarang'] = array();
    
    // ini data sample nya
    $_SESSION['dataBarang'][] = new TokoElektronik("TV001", "43\" The Frame Samsung", 11999000, 15, "uploads/sample-tv.jpg");
    $_SESSION['dataBarang'][] = new TokoElektronik("HP001", "iPhone 17 Pro Max", 19719000, 8, "uploads/sample-phone.jpg");
    $_SESSION['dataBarang'][] = new TokoElektronik("LP001", "MacBook Air M4", 17999000, 12, "uploads/sample-laptop.jpeg");
}

// variabel pesan sama form
$message = '';
$messageType = '';
$editBarang = null;
$isEdit = false;

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $result = handleAddBarang();
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            break;
            
        case 'update':
            $result = handleUpdateBarang();
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            break;
            
        case 'delete':
            $result = handleDeleteBarang();
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            break;
            
        case 'clear_all':
            foreach ($_SESSION['dataBarang'] as $barang) {
                if (!empty($barang->getGambar()) && file_exists($barang->getGambar())) {
                    unlink($barang->getGambar());
                }
            }
            $_SESSION['dataBarang'] = array();
            $message = 'Semua data berhasil dihapus!';
            $messageType = 'success';
            break;
    }
}

// ambil id buat di edit
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($_SESSION['dataBarang'] as $barang) {
        if ($barang->getIdBarang() === $editId) {
            $editBarang = $barang;
            $isEdit = true;
            break;
        }
    }
}

// variabel untuk pencarian
$searchKeyword = $_GET['search'] ?? '';
$dataBarang = $_SESSION['dataBarang'];

if (!empty($searchKeyword)) {
    $dataBarang = array_filter($dataBarang, function($barang) use ($searchKeyword) {
        return stripos($barang->getIdBarang(), $searchKeyword) !== false ||
               stripos($barang->getNamaBarang(), $searchKeyword) !== false;
    });
}

// function untuk upload file
function handleFileUpload($inputName, $oldImagePath = '') {
    global $uploadDir;
    
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImagePath;
    }
    
    if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error saat upload file');
    }
    
    $file = $_FILES[$inputName];
    
    // handle ukuran file
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Ukuran file terlalu besar! Maksimal 5MB');
    }
    
    // handle format file (gambar) yg bisa di upload
    $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Tipe file tidak didukung! Gunakan JPG, PNG, atau GIF');
    }
    
    // kasih nama file custom
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'img_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // upload file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Gagal mengupload file');
    }
    
    // hapus file lama yang sama dengan file baru
    if (!empty($oldImagePath) && file_exists($oldImagePath) && $oldImagePath !== $filePath) {
        unlink($oldImagePath);
    }
    
    return $filePath;
}

// function buat nambah barang
function handleAddBarang() {
    try {
        $idBarang = trim($_POST['idBarang'] ?? '');
        $namaBarang = trim($_POST['namaBarang'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $stok = intval($_POST['stok'] ?? 0);
        
        // handle kalau id barang atau nama barang kosong
        if (empty($idBarang) || empty($namaBarang)) {
            return array('success' => false, 'message' => 'ID Barang dan Nama Barang harus diisi!');
        }
        
        // handle kalau harga dan stok negatif
        if ($harga < 0 || $stok < 0) {
            return array('success' => false, 'message' => 'Harga dan Stok tidak boleh negatif!');
        }
        
        // buat cek apakah id nya udah ada
        foreach ($_SESSION['dataBarang'] as $barang) {
            if ($barang->getIdBarang() === $idBarang) {
                return array('success' => false, 'message' => 'ID Barang sudah ada!');
            }
        }
        
        // upload gambar
        $gambarPath = handleFileUpload('gambar');
        
        // bikin object barang baru
        $barangBaru = new TokoElektronik($idBarang, $namaBarang, $harga, $stok, $gambarPath);
        
        $validation = $barangBaru->validate();
        if (!$validation['valid']) {
            return array('success' => false, 'message' => implode(', ', $validation['errors']));
        }
        
        $_SESSION['dataBarang'][] = $barangBaru;
        
        return array('success' => true, 'message' => 'Barang berhasil ditambahkan!');
        
    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage());
    }
}

// function untuk update barang
function handleUpdateBarang() {
    try {
        $idBarangLama = trim($_POST['idBarangLama'] ?? '');
        $idBarang = trim($_POST['idBarang'] ?? '');
        $namaBarang = trim($_POST['namaBarang'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $stok = intval($_POST['stok'] ?? 0);
        
        // handle kalau id barang atau nama barang kosong
        if (empty($idBarang) || empty($namaBarang)) {
            return array('success' => false, 'message' => 'ID Barang dan Nama Barang harus diisi!');
        }
        
        // handle kalau harga tau stok negatif
        if ($harga < 0 || $stok < 0) {
            return array('success' => false, 'message' => 'Harga dan Stok tidak boleh negatif!');
        }
        
        // cari barang yang akan di update
        $foundIndex = -1;
        $oldImagePath = '';
        foreach ($_SESSION['dataBarang'] as $index => $barang) {
            if ($barang->getIdBarang() === $idBarangLama) {
                $foundIndex = $index;
                $oldImagePath = $barang->getGambar();
                break;
            }
        }
        
        // handle kalau barang gk ketemu
        if ($foundIndex === -1) {
            return array('success' => false, 'message' => 'Barang tidak ditemukan!');
        }
        
        // handle buat ganti gambar
        $gambarPath = handleFileUpload('gambar', $oldImagePath);
        
        $_SESSION['dataBarang'][$foundIndex] = new TokoElektronik($idBarang, $namaBarang, $harga, $stok, $gambarPath);
        
        return array('success' => true, 'message' => 'Barang berhasil diupdate!');
        
    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage());
    }
}

// function untuk delete barang
function handleDeleteBarang() {
    $idBarang = trim($_POST['idBarang'] ?? '');
    
    foreach ($_SESSION['dataBarang'] as $index => $barang) {
        if ($barang->getIdBarang() === $idBarang) {
            if (!empty($barang->getGambar()) && file_exists($barang->getGambar())) {
                unlink($barang->getGambar());
            }
            
            array_splice($_SESSION['dataBarang'], $index, 1);
            return array('success' => true, 'message' => 'Barang berhasil dihapus!');
        }
    }
    
    return array('success' => false, 'message' => 'Barang tidak ditemukan!');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Elektronik</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Sistem Manajemen Toko Elektronik</h1>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="form-section">
            <h2><?php echo $isEdit ? 'Edit Barang' : 'Tambah Barang'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'add'; ?>">
                <?php if ($isEdit): ?>
                <input type="hidden" name="idBarangLama" value="<?php echo htmlspecialchars($editBarang->getIdBarang()); ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="idBarang">ID Barang *</label>
                        <input type="text" id="idBarang" name="idBarang" required
                               value="<?php echo $isEdit ? htmlspecialchars($editBarang->getIdBarang()) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="namaBarang">Nama Barang *</label>
                        <input type="text" id="namaBarang" name="namaBarang" required
                               value="<?php echo $isEdit ? htmlspecialchars($editBarang->getNamaBarang()) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="harga">Harga (Rp) *</label>
                        <input type="number" id="harga" name="harga" min="0" step="1000" required
                               value="<?php echo $isEdit ? $editBarang->getHarga() : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="stok">Stok *</label>
                        <input type="number" id="stok" name="stok" min="0" required
                               value="<?php echo $isEdit ? $editBarang->getStok() : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="gambar">Upload Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <small>Format: JPG, PNG, GIF. Maksimal 5MB</small>
                        
                        <?php if ($isEdit && !empty($editBarang->getGambar()) && file_exists($editBarang->getGambar())): ?>
                        <div style="margin-top: 10px;">
                            <strong>Gambar saat ini:</strong><br>
                            <img src="<?php echo htmlspecialchars($editBarang->getGambar()); ?>" 
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 2px solid #ddd;">
                            <br><small>Upload gambar baru untuk mengganti</small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-success">
                        <?php echo $isEdit ? 'Update Barang' : 'Tambah Barang'; ?>
                    </button>
                    
                    <?php if ($isEdit): ?>
                    <a href="main.php" class="btn btn-danger">Batal Edit</a>
                    <?php endif; ?>
                    
                    <button type="reset" class="btn">Reset</button>
                </div>
            </form>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h2>Cari Barang</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Cari berdasarkan ID atau Nama Barang..." 
                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
                
                <button type="submit" class="btn btn">Cari</button>
                <a href="main.php" class="btn btn-danger">Batal</a>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <h2>Daftar Barang</h2>
            
            <?php if (!empty($searchKeyword)): ?>
            <p>Hasil Pencarian: "<strong><?php echo htmlspecialchars($searchKeyword); ?></strong>" 
               (<?php echo count($dataBarang); ?> Item ditemukan)</p>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dataBarang)): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <h3></h3>
                                <p><?php echo !empty($searchKeyword) ? 'Tidak ada data yang cocok dengan pencarian' : 'Belum ada data barang'; ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($dataBarang as $barang): ?>
                        <tr>
                            <td><strong><?php echo $no++; ?></strong></td>
                            <td>
                                <?php if (!empty($barang->getGambar()) && file_exists($barang->getGambar())): ?>
                                    <img src="<?php echo htmlspecialchars($barang->getGambar()); ?>" 
                                         alt="<?php echo htmlspecialchars($barang->getNamaBarang()); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($barang->getIdBarang()); ?></strong></td>
                            <td><?php echo htmlspecialchars($barang->getNamaBarang()); ?></td>
                            <td class="price"><?php echo $barang->getFormattedHarga(); ?></td>
                            <td><?php echo $barang->getStok(); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit=<?php echo urlencode($barang->getIdBarang()); ?>" 
                                       class="btn btn-small">Edit</a>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="idBarang" value="<?php echo htmlspecialchars($barang->getIdBarang()); ?>">
                                        <button type="submit" class="btn btn-danger btn-small" 
                                                onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; padding: 20px; color: #666;">
            <p>&copy 2025 Toko Elektronik</p>
        </div>
    </div>
</body>
</html>