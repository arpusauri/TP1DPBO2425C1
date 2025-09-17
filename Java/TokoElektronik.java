import java.util.ArrayList;
import java.util.Scanner;

/**
 * Class TokoElektronik untuk mengelola data barang elektronik
 * Berisi semua method CRUD dan utility functions
 */
public class TokoElektronik {
    // Atribut private (encapsulation)
    private String idBarang;
    private String namaBarang;
    private double harga;
    private int stok;
    
    // Static ArrayList untuk menyimpan semua data barang
    private static ArrayList<TokoElektronik> dataBarang = new ArrayList<>();
    
    // Static Scanner untuk input
    private static Scanner scanner = new Scanner(System.in);
    
    /**
     * Constructor untuk inisialisasi objek TokoElektronik
     * @param idBarang ID unik barang
     * @param namaBarang Nama barang
     * @param harga Harga barang
     * @param stok Jumlah stok barang
     */
    public TokoElektronik(String idBarang, String namaBarang, double harga, int stok) {
        this.idBarang = idBarang;
        this.namaBarang = namaBarang;
        this.harga = harga;
        this.stok = stok;
    }
    
    // ==================== GETTER METHODS ====================
    /**
     * Getter untuk ID barang
     * @return ID barang
     */
    public String getIdBarang() {
        return idBarang;
    }
    
    /**
     * Getter untuk nama barang
     * @return Nama barang
     */
    public String getNamaBarang() {
        return namaBarang;
    }
    
    /**
     * Getter untuk harga barang
     * @return Harga barang
     */
    public double getHarga() {
        return harga;
    }
    
    /**
     * Getter untuk stok barang
     * @return Stok barang
     */
    public int getStok() {
        return stok;
    }
    
    // ==================== SETTER METHODS ====================
    /**
     * Setter untuk ID barang
     * @param idBarang ID barang baru
     */
    public void setIdBarang(String idBarang) {
        this.idBarang = idBarang;
    }
    
    /**
     * Setter untuk nama barang
     * @param namaBarang Nama barang baru
     */
    public void setNamaBarang(String namaBarang) {
        this.namaBarang = namaBarang;
    }
    
    /**
     * Setter untuk harga barang
     * @param harga Harga barang baru
     */
    public void setHarga(double harga) {
        this.harga = harga;
    }
    
    /**
     * Setter untuk stok barang
     * @param stok Stok barang baru
     */
    public void setStok(int stok) {
        this.stok = stok;
    }
    
    /**
     * Method untuk menampilkan data barang dalam format tabel
     */
    public void displayBarang() {
        System.out.printf("%-10s %-25s Rp %,12.0f %8d unit%n", 
                         idBarang, namaBarang, harga, stok);
    }
    
    // ==================== STATIC METHODS CRUD ====================
    
    /**
     * Method untuk menampilkan menu utama
     */
    public static void tampilkanMenu() {
        System.out.println("\n");
        System.out.println("=====================================");
        System.out.println("     SISTEM MANAJEMEN TOKO ELEKTRONIK");
        System.out.println("=====================================");
        System.out.println("1. TAMPILKAN DATA");
        System.out.println("2. TAMBAHKAN DATA");
        System.out.println("3. UBAH DATA");
        System.out.println("4. HAPUS DATA");
        System.out.println("5. CARI DATA");
        System.out.println("6. EXIT");
        System.out.println("=====================================");
    }
    
    /**
     * Method untuk menampilkan semua data barang
     */
    public static void tampilkanSemuaData() {
        System.out.println("\n=== DAFTAR SEMUA BARANG ELEKTRONIK ===");
        
        if (dataBarang.isEmpty()) {
            System.out.println("Tidak ada data barang.");
            return;
        }
        
        System.out.println("----------------------------------------------------------------------");
        System.out.printf("%-10s %-25s %-15s %s%n", "ID", "Nama Barang", "Harga", "Stok");
        System.out.println("----------------------------------------------------------------------");
        
        for (TokoElektronik barang : dataBarang) {
            barang.displayBarang();
        }
        
        System.out.println("----------------------------------------------------------------------");
        System.out.println("Total barang: " + dataBarang.size() + " item");
    }
    
    /**
     * Method untuk mencari index barang berdasarkan ID
     * @param id ID barang yang dicari
     * @return Index barang atau -1 jika tidak ditemukan
     */
    public static int cariIndexById(String id) {
        for (int i = 0; i < dataBarang.size(); i++) {
            if (dataBarang.get(i).getIdBarang().equals(id)) {
                return i;
            }
        }
        return -1; // Tidak ditemukan
    }
    
    /**
     * Method untuk menambah data barang baru
     */
    public static void tambahData() {
        System.out.println("\n=== TAMBAH DATA BARANG BARU ===");
        
        System.out.print("Masukkan ID Barang: ");
        String idBarang = scanner.nextLine().trim();
        
        // Cek apakah ID sudah ada
        if (cariIndexById(idBarang) != -1) {
            System.out.println("Error: ID Barang sudah ada!");
            return;
        }
        
        System.out.print("Masukkan Nama Barang: ");
        String namaBarang = scanner.nextLine().trim();
        
        double harga;
        int stok;
        
        try {
            System.out.print("Masukkan Harga: Rp ");
            harga = Double.parseDouble(scanner.nextLine());
            
            System.out.print("Masukkan Stok: ");
            stok = Integer.parseInt(scanner.nextLine());
        } catch (NumberFormatException e) {
            System.out.println("Error: Input harga dan stok harus berupa angka!");
            return;
        }
        
        if (harga < 0 || stok < 0) {
            System.out.println("Error: Harga dan stok tidak boleh negatif!");
            return;
        }
        
        // Tambah barang baru ke ArrayList
        TokoElektronik barangBaru = new TokoElektronik(idBarang, namaBarang, harga, stok);
        dataBarang.add(barangBaru);
        System.out.println("\nData berhasil ditambahkan!");
    }
    
    /**
     * Method untuk mengupdate data barang
     */
    public static void updateData() {
        System.out.println("\n=== UPDATE DATA BARANG ===");
        
        if (dataBarang.isEmpty()) {
            System.out.println("Tidak ada data untuk diupdate.");
            return;
        }
        
        System.out.print("Masukkan ID Barang yang akan diupdate: ");
        String idBarang = scanner.nextLine().trim();
        
        int index = cariIndexById(idBarang);
        if (index == -1) {
            System.out.println("Barang dengan ID " + idBarang + " tidak ditemukan!");
            return;
        }
        
        System.out.println("\nData saat ini:");
        System.out.println("----------------------------------------------------------------------");
        System.out.printf("%-10s %-25s %-15s %s%n", "ID", "Nama Barang", "Harga", "Stok");
        System.out.println("----------------------------------------------------------------------");
        dataBarang.get(index).displayBarang();
        System.out.println("----------------------------------------------------------------------");
        
        System.out.println("\nMasukkan data baru:");
        System.out.print("Nama Barang: ");
        String namaBarang = scanner.nextLine().trim();
        
        double harga;
        int stok;
        
        try {
            System.out.print("Harga: Rp ");
            harga = Double.parseDouble(scanner.nextLine());
            
            System.out.print("Stok: ");
            stok = Integer.parseInt(scanner.nextLine());
        } catch (NumberFormatException e) {
            System.out.println("Error: Input harga dan stok harus berupa angka!");
            return;
        }
        
        if (harga < 0 || stok < 0) {
            System.out.println("Error: Harga dan stok tidak boleh negatif!");
            return;
        }
        
        // Update data menggunakan setter
        TokoElektronik barang = dataBarang.get(index);
        barang.setNamaBarang(namaBarang);
        barang.setHarga(harga);
        barang.setStok(stok);
        
        System.out.println("\nData berhasil diupdate!");
    }
    
    /**
     * Method untuk menghapus data barang
     */
    public static void hapusData() {
        System.out.println("\n=== HAPUS DATA BARANG ===");
        
        if (dataBarang.isEmpty()) {
            System.out.println("Tidak ada data untuk dihapus.");
            return;
        }
        
        System.out.print("Masukkan ID Barang yang akan dihapus: ");
        String idBarang = scanner.nextLine().trim();
        
        int index = cariIndexById(idBarang);
        if (index == -1) {
            System.out.println("Barang dengan ID " + idBarang + " tidak ditemukan!");
            return;
        }
        
        System.out.println("\nData yang akan dihapus:");
        System.out.println("----------------------------------------------------------------------");
        System.out.printf("%-10s %-25s %-15s %s%n", "ID", "Nama Barang", "Harga", "Stok");
        System.out.println("----------------------------------------------------------------------");
        dataBarang.get(index).displayBarang();
        System.out.println("----------------------------------------------------------------------");
        
        System.out.print("\nApakah Anda yakin ingin menghapus data ini? (y/n): ");
        String konfirmasi = scanner.nextLine().trim().toLowerCase();
        
        if (konfirmasi.equals("y")) {
            dataBarang.remove(index);
            System.out.println("\nData berhasil dihapus!");
        } else {
            System.out.println("\nPenghapusan dibatalkan.");
        }
    }
    
    /**
     * Method untuk mencari data barang berdasarkan ID
     */
    public static void cariData() {
        System.out.println("\n=== CARI DATA BARANG ===");
        
        if (dataBarang.isEmpty()) {
            System.out.println("Tidak ada data untuk dicari.");
            return;
        }
        
        System.out.print("Masukkan ID Barang yang dicari: ");
        String idBarang = scanner.nextLine().trim();
        
        int index = cariIndexById(idBarang);
        if (index == -1) {
            System.out.println("Barang dengan ID " + idBarang + " tidak ditemukan!");
            return;
        }
        
        System.out.println("\n=== DATA DITEMUKAN ===");
        System.out.println("----------------------------------------------------------------------");
        System.out.printf("%-10s %-25s %-15s %s%n", "ID", "Nama Barang", "Harga", "Stok");
        System.out.println("----------------------------------------------------------------------");
        dataBarang.get(index).displayBarang();
        System.out.println("----------------------------------------------------------------------");
    }
    
    /**
     * Method untuk inisialisasi data sample
     */
    public static void initSampleData() {
        dataBarang.add(new TokoElektronik("TV001", "43\" The Frame 4K Samsung Smart TV", 11999000, 15));
        dataBarang.add(new TokoElektronik("HP001", "iPhone 17 Pro Max", 19719000, 8));
        dataBarang.add(new TokoElektronik("LP001", "MacBook Air M4", 17999000, 12));
    }
    
    /**
     * Method untuk menutup Scanner (clean up resources)
     */
    public static void closeScanner() {
        scanner.close();
    }
}