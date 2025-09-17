class TokoElektronik:
    # constructor
    def __init__(self, id_barang, nama_barang, harga, stok):
        self.__id_barang = id_barang
        self.__nama_barang = nama_barang 
        self.__harga = harga
        self.__stok = stok
    
    # getter
    def get_id_barang(self):
        return self.__id_barang
    
    def get_nama_barang(self):
        return self.__nama_barang
    
    def get_harga(self):
        return self.__harga
    
    def get_stok(self):
        return self.__stok
    
    # setter 
    def set_id_barang(self, id_barang):
        self.__id_barang = id_barang
    
    def set_nama_barang(self, nama_barang):
        self.__nama_barang = nama_barang
    
    def set_harga(self, harga):
        self.__harga = harga
    
    def set_stok(self, stok):
        self.__stok = stok
    
    def display_barang(self):
        print(f"{self.__id_barang:<10} {self.__nama_barang:<25} Rp {self.__harga:>12,.0f} {self.__stok:>8} unit")
    
    def __del__(self):
        pass

# global list untuk menyimpan data barang
data_barang = []

# fungsi tampilkan menu
def tampilkan_menu():
    print("\n")
    print("=" * 37)
    print("  SISTEM MANAJEMEN TOKO ELEKTRONIK")
    print("=" * 37)
    print("1. TAMPILKAN DATA")
    print("2. TAMBAHKAN DATA")
    print("3. UBAH DATA")
    print("4. HAPUS DATA")
    print("5. CARI DATA")
    print("6. EXIT")
    print("=" * 37)

# fungsi tampilkan semua data
def tampilkan_semua_data():
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

# fungsi mencari index berdasarkan id
def cari_index_by_id(id_barang):
    for i, barang in enumerate(data_barang):
        if barang.get_id_barang() == id_barang:
            return i
    return -1

# fungsi untuk menambahkan suatu data
def tambah_data():
    print("\n=== TAMBAH DATA BARANG BARU ===")
    
    id_barang = input("Masukkan ID Barang: ").strip()
    
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
    
    
    barang_baru = TokoElektronik(id_barang, nama_barang, harga, stok)
    data_barang.append(barang_baru)
    print("\nData berhasil ditambahkan!")

# fungsi untuk mengubah data
def update_data():
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
    
    # update data
    data_barang[index].set_nama_barang(nama_barang)
    data_barang[index].set_harga(harga)
    data_barang[index].set_stok(stok)
    
    print("\nData berhasil diupdate!")

# fungsi untuk hapus suatu data
def hapus_data():
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

# fungsi untuk mencari data
def cari_data():
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

# fungsi bikin sample data
def init_sample_data():
    sample_data = [
        TokoElektronik("TV001", "43\" The Frame Samsung", 11999000, 15),
        TokoElektronik("HP001", "iPhone 17 Pro Max", 19719000, 8),
        TokoElektronik("LP001", "MacBook Air M4", 17999000, 12)
    ]
    
    data_barang.extend(sample_data)