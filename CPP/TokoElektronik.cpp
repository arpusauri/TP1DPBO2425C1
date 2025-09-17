#include <iostream>
#include <vector>
#include <string>
#include <iomanip>

using namespace std;

class TokoElektronik {
// atribut dari class semuanya dijadikan private
private:
    string idBarang; // tipe data string agar bisa menggunakan kombinasi huruf dan angka
    string namaBarang;
    double harga;
    int stok;

public:
    // constructor
    TokoElektronik(string idBarang, string namaBarang, double harga, int stok) {
        this->idBarang = idBarang;
        this->namaBarang = namaBarang;
        this->harga = harga;
        this->stok = stok;
    }

    // getter
    string getIdBarang() const {
        return idBarang;
    }
    string getNamaBarang() const {
        return namaBarang;
    }
    double getHarga() const {
        return harga;
    }
    int getStok() const {
        return stok;
    }

    // setter
    void setIdBarang(string idBarang) {
        this->idBarang = idBarang;
    }
    void setNamaBarang(string namaBarang) {
        this->namaBarang = namaBarang;
    }
    void setHarga(double harga) {
        this->harga = harga;
    }
    void setStok(int stok) {
        this->stok = stok;
    }

    // method untuk display data
    void displayBarang() const {
        cout << left << setw(10) << idBarang 
             << setw(25) << namaBarang 
             << "Rp " << right << setw(12) << fixed << setprecision(0) << harga 
             << setw(8) << stok << " unit" << endl;
    }

    // destructor
    ~TokoElektronik() { }
};

// global vector untuk menyimpan data
vector<TokoElektronik> dataBarang;

// fungsi untuk menampilkan menu awal
void tampilkanMenu() {
    cout << "\n";
    cout << "=====================================" << endl;
    cout << "  SISTEM MANAJEMEN TOKO ELEKTRONIK" << endl;
    cout << "=====================================" << endl;
    cout << "1. Tampilkan semua data" << endl;
    cout << "2. Tambah data" << endl;
    cout << "3. Update data" << endl;
    cout << "4. Hapus data" << endl;
    cout << "5. Cari data" << endl;
    cout << "6. Keluar" << endl;
    cout << "=====================================" << endl;
}

// fungsi untuk menampilkan semua data
void tampilkanSemuaData() {
    cout << "\n=== DAFTAR SEMUA BARANG ELEKTRONIK ===" << endl;
    
    if (dataBarang.empty()) {
        cout << "Tidak ada data barang." << endl;
        return;
    }
    
    cout << string(70, '-') << endl;
    cout << left << setw(10) << "ID" 
         << setw(25) << "Nama Barang" 
         << setw(15) << "Harga" 
         << "Stok" << endl;
    cout << string(70, '-') << endl;
    
    for (const auto& barang : dataBarang) {
        barang.displayBarang();
    }
    cout << string(70, '-') << endl;
    cout << "Total barang: " << dataBarang.size() << " item" << endl;
}

// fungsi untuk mencari index berdasarkan ID
int cariIndexById(string id) {
    for (int i = 0; i < dataBarang.size(); i++) {
        if (dataBarang[i].getIdBarang() == id) {
            return i;
        }
    }
    return -1; // tidak ditemukan
}

// fungsi untuk menambah data
void tambahData() {
    cout << "\n=== TAMBAH DATA BARANG BARU ===" << endl;
    
    string id, nama;
    double harga;
    int stok;
    
    cout << "Masukkan ID Barang: ";
    getline(cin, id);
    
    // Cek apakah ID sudah ada
    if (cariIndexById(id) != -1) {
        cout << "Error: ID Barang sudah ada!" << endl;
        return;
    }
    
    cout << "Masukkan Nama Barang: ";
    getline(cin, nama);
    
    cout << "Masukkan Harga: Rp ";
    cin >> harga;
    
    cout << "Masukkan Stok: ";
    cin >> stok;
    
    if (harga < 0 || stok < 0) {
        cout << "Error: Harga dan stok tidak boleh negatif!" << endl;
        return;
    }
    
    dataBarang.push_back(TokoElektronik(id, nama, harga, stok));
    cout << "\nData berhasil ditambahkan!" << endl;
}

// fungsi untuk update data
void updateData() {
    cout << "\n=== UPDATE DATA BARANG ===" << endl;
    
    if (dataBarang.empty()) {
        cout << "Tidak ada data untuk diupdate." << endl;
        return;
    }
    
    string id;
    cout << "Masukkan ID Barang yang akan diupdate: ";
    getline(cin, id);
    
    int index = cariIndexById(id);
    if (index == -1) {
        cout << "Barang dengan ID " << id << " tidak ditemukan!" << endl;
        return;
    }
    
    cout << "\nData saat ini:" << endl;
    cout << string(70, '-') << endl;
    cout << left << setw(10) << "ID" 
         << setw(25) << "Nama Barang" 
         << setw(15) << "Harga" 
         << "Stok" << endl;
    cout << string(70, '-') << endl;
    dataBarang[index].displayBarang();
    cout << string(70, '-') << endl;
    
    string nama;
    double harga;
    int stok;
    
    cout << "\nMasukkan data baru:" << endl;
    cout << "Nama Barang: ";
    getline(cin, nama);
    
    cout << "Harga: Rp ";
    cin >> harga;
    
    cout << "Stok: ";
    cin >> stok;
    
    if (harga < 0 || stok < 0) {
        cout << "Error: Harga dan stok tidak boleh negatif!" << endl;
        return;
    }
    
    dataBarang[index].setNamaBarang(nama);
    dataBarang[index].setHarga(harga);
    dataBarang[index].setStok(stok);
    
    cout << "\nData berhasil diupdate!" << endl;
}

// fungsi untuk hapus data
void hapusData() {
    cout << "\n=== HAPUS DATA BARANG ===" << endl;
    
    if (dataBarang.empty()) {
        cout << "Tidak ada data untuk dihapus." << endl;
        return;
    }
    
    string id;
    cout << "Masukkan ID Barang yang akan dihapus: ";
    getline(cin, id);
    
    int index = cariIndexById(id);
    if (index == -1) {
        cout << "Barang dengan ID " << id << " tidak ditemukan!" << endl;
        return;
    }
    
    cout << "\nData yang akan dihapus:" << endl;
    cout << string(70, '-') << endl;
    cout << left << setw(10) << "ID" 
         << setw(25) << "Nama Barang" 
         << setw(15) << "Harga" 
         << "Stok" << endl;
    cout << string(70, '-') << endl;
    dataBarang[index].displayBarang();
    cout << string(70, '-') << endl;
    
    char konfirmasi;
    cout << "\nApakah Anda yakin ingin menghapus data ini? (y/n): ";
    cin >> konfirmasi;
    
    if (konfirmasi == 'y' || konfirmasi == 'Y') {
        dataBarang.erase(dataBarang.begin() + index);
        cout << "\nData berhasil dihapus!" << endl;
    } else {
        cout << "\nPenghapusan dibatalkan." << endl;
    }
}

// fungsi untuk cari data
void cariData() {
    cout << "\n=== CARI DATA BARANG ===" << endl;
    
    if (dataBarang.empty()) {
        cout << "Tidak ada data untuk dicari." << endl;
        return;
    }
    
    string id;
    cout << "Masukkan ID Barang yang dicari: ";
    getline(cin, id);
    
    int index = cariIndexById(id);
    if (index == -1) {
        cout << "Barang dengan ID " << id << " tidak ditemukan!" << endl;
        return;
    }
    
    cout << "\n=== DATA DITEMUKAN ===" << endl;
    cout << string(70, '-') << endl;
    cout << left << setw(10) << "ID" 
         << setw(25) << "Nama Barang" 
         << setw(15) << "Harga" 
         << "Stok" << endl;
    cout << string(70, '-') << endl;
    dataBarang[index].displayBarang();
    cout << string(70, '-') << endl;
}

// fungsi untuk inisialisasi data sample
void initSampleData() {
    dataBarang.push_back(TokoElektronik("TV001", "Smart TV Samsung 43\"", 5500000, 15));
    dataBarang.push_back(TokoElektronik("HP001", "iPhone 15 Pro Max", 18900000, 8));
    dataBarang.push_back(TokoElektronik("LP001", "MacBook Air M2", 16500000, 12));
}