<?php
// Include class TokoElektronik
require_once 'TokoElektronik.php';

// Buat folder uploads jika belum ada
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Array untuk menyimpan data barang (menggunakan file session)
session_start();

// Inisialisasi data jika belum ada
if (!isset($_SESSION['dataBarang'])) {
    $_SESSION['dataBarang'] = array();
    
    // Data sample awal
    $_SESSION['dataBarang'][] = new TokoElektronik("TV001", "Samsung Smart TV 43 Inch", 5500000, 15, "uploads/sample-tv.jpg");
    $_SESSION['dataBarang'][] = new TokoElektronik("HP001", "iPhone 15 Pro Max", 18900000, 8, "uploads/sample-phone.jpg");
    $_SESSION['dataBarang'][] = new TokoElektronik("LP001", "MacBook Air M2", 16500000, 12, "uploads/sample-laptop.jpg");
}

// Variabel untuk pesan dan form
$message = '';
$messageType = '';
$editBarang = null;
$isEdit = false;

// Handle form submission
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
            // Hapus semua file gambar
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

// Handle GET parameters untuk edit
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

// Handle pencarian
$searchKeyword = $_GET['search'] ?? '';
$dataBarang = $_SESSION['dataBarang'];

if (!empty($searchKeyword)) {
    $dataBarang = array_filter($dataBarang, function($barang) use ($searchKeyword) {
        return stripos($barang->getIdBarang(), $searchKeyword) !== false ||
               stripos($barang->getNamaBarang(), $searchKeyword) !== false;
    });
}

// Function untuk handle upload file
function handleFileUpload($inputName, $oldImagePath = '') {
    global $uploadDir;
    
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImagePath;
    }
    
    if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error saat upload file');
    }
    
    $file = $_FILES[$inputName];
    
    // Validasi ukuran file (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Ukuran file terlalu besar! Maksimal 5MB');
    }
    
    // Validasi tipe file
    $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Tipe file tidak didukung! Gunakan JPG, PNG, atau GIF');
    }
    
    // Generate nama file unik
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'img_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Gagal mengupload file');
    }
    
    // Hapus file lama jika ada
    if (!empty($oldImagePath) && file_exists($oldImagePath) && $oldImagePath !== $filePath) {
        unlink($oldImagePath);
    }
    
    return $filePath;
}

// Function untuk menambah barang
function handleAddBarang() {
    try {
        $idBarang = trim($_POST['idBarang'] ?? '');
        $namaBarang = trim($_POST['namaBarang'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $stok = intval($_POST['stok'] ?? 0);
        
        // Validasi basic
        if (empty($idBarang) || empty($namaBarang)) {
            return array('success' => false, 'message' => 'ID Barang dan Nama Barang harus diisi!');
        }
        
        if ($harga < 0 || $stok < 0) {
            return array('success' => false, 'message' => 'Harga dan Stok tidak boleh negatif!');
        }
        
        // Cek duplikasi ID
        foreach ($_SESSION['dataBarang'] as $barang) {
            if ($barang->getIdBarang() === $idBarang) {
                return array('success' => false, 'message' => 'ID Barang sudah ada!');
            }
        }
        
        // Handle upload gambar
        $gambarPath = handleFileUpload('gambar');
        
        // Buat object barang baru
        $barangBaru = new TokoElektronik($idBarang, $namaBarang, $harga, $stok, $gambarPath);
        
        // Validasi menggunakan method di class
        $validation = $barangBaru->validate();
        if (!$validation['valid']) {
            return array('success' => false, 'message' => implode(', ', $validation['errors']));
        }
        
        // Tambah ke array
        $_SESSION['dataBarang'][] = $barangBaru;
        
        return array('success' => true, 'message' => 'Barang berhasil ditambahkan!');
        
    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage());
    }
}

// Function untuk update barang
function handleUpdateBarang() {
    try {
        $idBarangLama = trim($_POST['idBarangLama'] ?? '');
        $idBarang = trim($_POST['idBarang'] ?? '');
        $namaBarang = trim($_POST['namaBarang'] ?? '');
        $harga = floatval($_POST['harga'] ?? 0);
        $stok = intval($_POST['stok'] ?? 0);
        
        // Validasi basic
        if (empty($idBarang) || empty($namaBarang)) {
            return array('success' => false, 'message' => 'ID Barang dan Nama Barang harus diisi!');
        }
        
        if ($harga < 0 || $stok < 0) {
            return array('success' => false, 'message' => 'Harga dan Stok tidak boleh negatif!');
        }
        
        // Cari barang yang akan diupdate
        $foundIndex = -1;
        $oldImagePath = '';
        foreach ($_SESSION['dataBarang'] as $index => $barang) {
            if ($barang->getIdBarang() === $idBarangLama) {
                $foundIndex = $index;
                $oldImagePath = $barang->getGambar();
                break;
            }
        }
        
        if ($foundIndex === -1) {
            return array('success' => false, 'message' => 'Barang tidak ditemukan!');
        }
        
        // Handle upload gambar (jika ada file baru)
        $gambarPath = handleFileUpload('gambar', $oldImagePath);
        
        // Update barang
        $_SESSION['dataBarang'][$foundIndex] = new TokoElektronik($idBarang, $namaBarang, $harga, $stok, $gambarPath);
        
        return array('success' => true, 'message' => 'Barang berhasil diupdate!');
        
    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage());
    }
}

// Function untuk delete barang
function handleDeleteBarang() {
    $idBarang = trim($_POST['idBarang'] ?? '');
    
    foreach ($_SESSION['dataBarang'] as $index => $barang) {
        if ($barang->getIdBarang() === $idBarang) {
            // Hapus file gambar jika ada
            if (!empty($barang->getGambar()) && file_exists($barang->getGambar())) {
                unlink($barang->getGambar());
            }
            
            array_splice($_SESSION['dataBarang'], $index, 1);
            return array('success' => true, 'message' => 'Barang berhasil dihapus!');
        }
    }
    
    return array('success' => false, 'message' => 'Barang tidak ditemukan!');
}

// Hitung statistik
$totalBarang = count($_SESSION['dataBarang']);
$totalNilai = 0;
foreach ($_SESSION['dataBarang'] as $barang) {
    $totalNilai += $barang->getTotalNilai();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem CRUD Toko Elektronik - PHP OOP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-group small {
            color: #666;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219a52; }

        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }

        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .search-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #3498db;
        }

        .stat-card h3 {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #666;
        }

        .table-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #ddd;
        }

        .no-image {
            width: 60px;
            height: 60px;
            background-color: #f8f9fa;
            border: 2px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 12px;
        }

        .price {
            font-weight: bold;
            color: #27ae60;
        }

        .stock-badge {
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }

        .stock-high { background-color: #27ae60; }
        .stock-medium { background-color: #f39c12; }
        .stock-low { background-color: #e74c3c; }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state h3 {
            font-size: 3em;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-input {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .data-table {
                font-size: 14px;
            }
            
            .data-table th,
            .data-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏪 Sistem Manajemen Toko Elektronik</h1>
            <p>Kelola inventori barang elektronik dengan PHP OOP</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="form-section">
            <h2><?php echo $isEdit ? '✏️ Edit Barang' : '➕ Tambah Barang Baru'; ?></h2>
            
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
                        💾 <?php echo $isEdit ? 'Update Barang' : 'Simpan Barang'; ?>
                    </button>
                    
                    <?php if ($isEdit): ?>
                    <a href="index.php" class="btn btn-danger">❌ Batal Edit</a>
                    <?php endif; ?>
                    
                    <button type="reset" class="btn">🔄 Reset Form</button>
                </div>
            </form>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Cari berdasarkan ID atau Nama Barang..." 
                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
                
                <button type="submit" class="btn">🔍 Cari</button>
                <a href="index.php" class="btn">📋 Reset</a>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="btn btn-warning" 
                            onclick="return confirm('Yakin ingin menghapus semua data?')">
                        🗑️ Hapus Semua
                    </button>
                </form>
            </form>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stat-card">
                <h3><?php echo $totalBarang; ?></h3>
                <p>Total Produk</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($totalNilai, 0, ',', '.'); ?></h3>
                <p>Total Nilai Stok (Rp)</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalBarang > 0 ? number_format($totalNilai / $totalBarang, 0, ',', '.') : '0'; ?></h3>
                <p>Rata-rata Nilai (Rp)</p>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <h2>📋 Daftar Barang</h2>
            
            <?php if (!empty($searchKeyword)): ?>
            <p>🔍 Hasil Pencarian: "<strong><?php echo htmlspecialchars($searchKeyword); ?></strong>" 
               (<?php echo count($dataBarang); ?> item ditemukan)</p>
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
                        <th>Total Nilai</th>
                        <th>Status Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dataBarang)): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <h3>📦</h3>
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
                            <td class="price"><?php echo $barang->getFormattedTotalNilai(); ?></td>
                            <td>
                                <span class="stock-badge <?php echo $barang->getStokClass(); ?>">
                                    <?php echo $barang->getStatusStok(); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit=<?php echo urlencode($barang->getIdBarang()); ?>" 
                                       class="btn btn-small">✏️ Edit</a>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="idBarang" value="<?php echo htmlspecialchars($barang->getIdBarang()); ?>">
                                        <button type="submit" class="btn btn-danger btn-small" 
                                                onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            🗑️ Hapus
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
            <p>© 2024 Sistem Manajemen Toko Elektronik - PHP OOP</p>
            <p>Total Data: <strong><?php echo count($_SESSION['dataBarang']); ?></strong> | 
               Data Ditampilkan: <strong><?php echo count($dataBarang); ?></strong></p>
        </div>
    </div>
</body>
</html>