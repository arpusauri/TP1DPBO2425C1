<?php

class TokoElektronik {
    // atribut (private)
    private $idBarang;
    private $namaBarang;
    private $harga;
    private $stok;
    private $gambar; // atribut tambahan buat gambar
    
    // constructor
    public function __construct($idBarang, $namaBarang, $harga, $stok, $gambar = '') {
        $this->idBarang = $idBarang;
        $this->namaBarang = $namaBarang;
        $this->harga = $harga;
        $this->stok = $stok;
        $this->gambar = $gambar;
    }
    
    // getter
    public function getIdBarang(): string {
        return $this->idBarang;
    }
    public function getNamaBarang(): string {
        return $this->namaBarang;
    }
    public function getHarga() {
        return $this->harga;
    }
    public function getStok(): int {
        return $this->stok;
    }
    public function getGambar() {
        return $this->gambar;
    }
    
    // setter
    public function setIdBarang(string $idBarang): void {
        $this->idBarang = $idBarang;
    }
    
    public function setNamaBarang(string $namaBarang): void {
        $this->namaBarang = $namaBarang;
    }
    
    public function setHarga($harga) {
        $this->harga = $harga;
    }
    
    public function setStok(int $stok): void {
        $this->stok = $stok;
    }
    
    public function setGambar($gambar) {
        $this->gambar = $gambar;
    }
    
    // format rupiah
    public function getFormattedHarga() {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }    
    
    // validasi input
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
    
    // destructor
    public function __destruct() {
        
    }
}
?>