class TokoElektronik:
    def __init__(self, id_barang, nama_barang, harga, stok):
        """Constructor untuk inisialisasi objek TokoElektronik"""
        self.__id_barang = id_barang      # private attribute
        self.__nama_barang = nama_barang  # private attribute  
        self.__harga = harga              # private attribute
        self.__stok = stok                # private attribute
    
    # Getter methods
    def get_id_barang(self):
        """Getter untuk ID barang"""
        return self.__id_barang
    
    def get_nama_barang(self):
        """Getter untuk nama barang"""
        return self.__nama_barang
    
    def get_harga(self):
        """Getter untuk harga barang"""
        return self.__harga
    
    def get_stok(self):
        """Getter untuk stok barang"""
        return self.__stok
    
    # Setter methods
    def set_id_barang(self, id_barang):
        """Setter untuk ID barang"""
        self.__id_barang = id_barang
    
    def set_nama_barang(self, nama_barang):
        """Setter untuk nama barang"""
        self.__nama_barang = nama_barang
    
    def set_harga(self, harga):
        """Setter untuk harga barang"""
        self.__harga = harga
    
    def set_stok(self, stok):
        """Setter untuk stok barang"""
        self.__stok = stok
    
    def display_barang(self):
        """Method untuk menampilkan data barang dalam format tabel"""
        print(f"{self.__id_barang:<10} {self.__nama_barang:<25} Rp {self.__harga:>12,.0f} {self.__stok:>8} unit")
    
    def __del__(self):
        """Destructor (Python menggunakan garbage collection, jadi opsional)"""
        pass

# Global list untuk menyimpan data barang
data_barang = []

def tampilkan_menu():
    """Fungsi untuk menampilkan menu utama"""
    print("\n")
    print("=" * 37)
    print("     SISTEM MANAJEMEN TOKO ELEKTRONIK")
    print("=" * 37)
    print("1. TAMPILKAN DATA")
    print("2. TAMBAHKAN DATA")
    print("3. UBAH DATA")
    print("4. HAPUS DATA")
    print("5. CARI DATA")
    print("6. EXIT")
    print("=" * 37)

def tampilkan_semua_data():
    """Fungsi untuk menampilkan semua data barang"""
    print("\n=== DAFTAR SEMUA BARANG ELEKTRONIK ===")
    
    if not data_barang:
        print("Tidak ada data barang.")
        return
    
    print("-" * 70)
    print(f"{'ID':<10} {'Nama Barang':<25} {'Harga':<15} {'Stok'}")
    print("-" * 70)
    
    for barang in data_barang:
        barang.display_barang()
    
    print("-" * 70)
    print(f"Total barang: {len(data_barang)} item")

def cari_index_by_id(id_barang):
    """Fungsi untuk mencari index barang berdasarkan ID"""
    for i, barang in enumerate(data_barang):
        if barang.get_id_barang() == id_barang:
            return i
    return -1  # Tidak ditemukan

def tambah_data():
    """Fungsi untuk menambah data barang baru"""
    print("\n=== TAMBAH DATA BARANG BARU ===")
    
    id_barang = input("Masukkan ID Barang: ").strip()
    
    # Cek apakah ID sudah ada
    if cari_index_by_id(id_barang) != -1:
        print("Error: ID Barang sudah ada!")
        return
    
    nama_barang = input("Masukkan Nama Barang: ").strip()
    
    try:
        harga = float(input("Masukkan Harga: Rp "))
        stok = int(input("Masukkan Stok: "))
    except ValueError:
        print("Error: Input harga dan stok harus berupa angka!")
        return
    
    if harga < 0 or stok < 0:
        print("Error: Harga dan stok tidak boleh negatif!")
        return
    
    # Tambah barang baru ke list
    barang_baru = TokoElektronik(id_barang, nama_barang, harga, stok)
    data_barang.append(barang_baru)
    print("\nData berhasil ditambahkan!")

def update_data():
    """Fungsi untuk mengupdate data barang"""
    print("\n=== UPDATE DATA BARANG ===")
    
    if not data_barang:
        print("Tidak ada data untuk diupdate.")
        return
    
    id_barang = input("Masukkan ID Barang yang akan diupdate: ").strip()
    
    index = cari_index_by_id(id_barang)
    if index == -1:
        print(f"Barang dengan ID {id_barang} tidak ditemukan!")
        return
    
    print("\nData saat ini:")
    print("-" * 70)
    print(f"{'ID':<10} {'Nama Barang':<25} {'Harga':<15} {'Stok'}")
    print("-" * 70)
    data_barang[index].display_barang()
    print("-" * 70)
    
    print("\nMasukkan data baru:")
    nama_barang = input("Nama Barang: ").strip()
    
    try:
        harga = float(input("Harga: Rp "))
        stok = int(input("Stok: "))
    except ValueError:
        print("Error: Input harga dan stok harus berupa angka!")
        return
    
    if harga < 0 or stok < 0:
        print("Error: Harga dan stok tidak boleh negatif!")
        return
    
    # Update data menggunakan setter
    data_barang[index].set_nama_barang(nama_barang)
    data_barang[index].set_harga(harga)
    data_barang[index].set_stok(stok)
    
    print("\nData berhasil diupdate!")

def hapus_data():
    """Fungsi untuk menghapus data barang"""
    print("\n=== HAPUS DATA BARANG ===")
    
    if not data_barang:
        print("Tidak ada data untuk dihapus.")
        return
    
    id_barang = input("Masukkan ID Barang yang akan dihapus: ").strip()
    
    index = cari_index_by_id(id_barang)
    if index == -1:
        print(f"Barang dengan ID {id_barang} tidak ditemukan!")
        return
    
    print("\nData yang akan dihapus:")
    print("-" * 70)
    print(f"{'ID':<10} {'Nama Barang':<25} {'Harga':<15} {'Stok'}")
    print("-" * 70)
    data_barang[index].display_barang()
    print("-" * 70)
    
    konfirmasi = input("\nApakah Anda yakin ingin menghapus data ini? (y/n): ").strip().lower()
    
    if konfirmasi == 'y':
        data_barang.pop(index)
        print("\nData berhasil dihapus!")
    else:
        print("\nPenghapusan dibatalkan.")

def cari_data():
    """Fungsi untuk mencari data barang berdasarkan ID"""
    print("\n=== CARI DATA BARANG ===")
    
    if not data_barang:
        print("Tidak ada data untuk dicari.")
        return
    
    id_barang = input("Masukkan ID Barang yang dicari: ").strip()
    
    index = cari_index_by_id(id_barang)
    if index == -1:
        print(f"Barang dengan ID {id_barang} tidak ditemukan!")
        return
    
    print("\n=== DATA DITEMUKAN ===")
    print("-" * 70)
    print(f"{'ID':<10} {'Nama Barang':<25} {'Harga':<15} {'Stok'}")
    print("-" * 70)
    data_barang[index].display_barang()
    print("-" * 70)

def init_sample_data():
    """Fungsi untuk inisialisasi data sample"""
    sample_data = [
        TokoElektronik("TV001", "43\" The Frame Samsung", 11999000, 15),
        TokoElektronik("HP001", "iPhone 17 Pro Max", 19719000, 8),
        TokoElektronik("LP001", "MacBook Air M4", 17999000, 12)
    ]
    
    data_barang.extend(sample_data)