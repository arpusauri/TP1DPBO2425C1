<?php
/**
 * Class TokoElektronik
 * Class untuk mengelola data barang elektronik dengan konsep OOP
 */
class TokoElektronik {
    
    // Atribut private (Encapsulation)
    private $idBarang;
    private $namaBarang;
    private $harga;
    private $stok;
    private $gambar; // Path file gambar lokal
    
    /**
     * Constructor
     */
    public function __construct($idBarang, $namaBarang, $harga, $stok, $gambar = '') {
        $this->idBarang = $idBarang;
        $this->namaBarang = $namaBarang;
        $this->harga = $harga;
        $this->stok = $stok;
        $this->gambar = $gambar;
    }
    
    // ==================== GETTER METHODS ====================
    
    public function getIdBarang() {
        return $this->idBarang;
    }
    
    public function getNamaBarang() {
        return $this->namaBarang;
    }
    
    public function getHarga() {
        return $this->harga;
    }
    
    public function getStok() {
        return $this->stok;
    }
    
    public function getGambar() {
        return $this->gambar;
    }
    
    // ==================== SETTER METHODS ====================
    
    public function setIdBarang($idBarang) {
        $this->idBarang = $idBarang;
    }
    
    public function setNamaBarang($namaBarang) {
        $this->namaBarang = $namaBarang;
    }
    
    public function setHarga($harga) {
        $this->harga = $harga;
    }
    
    public function setStok($stok) {
        $this->stok = $stok;
    }
    
    public function setGambar($gambar) {
        $this->gambar = $gambar;
    }
    
    // ==================== BUSINESS METHODS ====================
    
    /**
     * Method untuk menghitung total nilai stok
     */
    public function getTotalNilai() {
        return $this->harga * $this->stok;
    }
    
    /**
     * Method untuk format harga dalam Rupiah
     */
    public function getFormattedHarga() {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
    
    /**
     * Method untuk format total nilai dalam Rupiah
     */
    public function getFormattedTotalNilai() {
        return 'Rp ' . number_format($this->getTotalNilai(), 0, ',', '.');
    }
    
    /**
     * Method untuk mendapatkan status stok
     */
    public function getStatusStok() {
        if ($this->stok > 10) {
            return 'Tinggi';
        } elseif ($this->stok > 5) {
            return 'Sedang';
        } else {
            return 'Rendah';
        }
    }
    
    /**
     * Method untuk mendapatkan CSS class berdasarkan stok
     */
    public function getStokClass() {
        if ($this->stok > 10) {
            return 'stock-high';
        } elseif ($this->stok > 5) {
            return 'stock-medium';
        } else {
            return 'stock-low';
        }
    }
    
    /**
     * Method untuk validasi data barang
     */
    public function validate() {
        $errors = array();
        
        if (empty($this->idBarang)) {
            $errors[] = 'ID Barang tidak boleh kosong';
        }
        
        if (empty($this->namaBarang)) {
            $errors[] = 'Nama Barang tidak boleh kosong';
        }
        
        if ($this->harga < 0) {
            $errors[] = 'Harga tidak boleh negatif';
        }
        
        if ($this->stok < 0) {
            $errors[] = 'Stok tidak boleh negatif';
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        // Cleanup jika diperlukan
    }
}
?>