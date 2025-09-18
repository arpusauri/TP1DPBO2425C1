import java.util.ArrayList;
import java.util.Scanner;

public class TokoElektronik {
    // atribut (private)
    private String idBarang;
    private String namaBarang;
    private double harga;
    private int stok;
    
    // static ArrayList untuk menyimpan semua data barang
    private static ArrayList<TokoElektronik> dataBarang = new ArrayList<>();
    
    // static scanner untuk input
    private static Scanner scanner = new Scanner(System.in);
     
    // constructor
    public TokoElektronik(String idBarang, String namaBarang, double harga, int stok) {
        this.idBarang = idBarang;
        this.namaBarang = namaBarang;
        this.harga = harga;
        this.stok = stok;
    }
    
    // getter
    public String getIdBarang() {
        return idBarang;
    }
    public String getNamaBarang() {
        return namaBarang;
    }
    public double getHarga() {
        return harga;
    }
    public int getStok() {
        return stok;
    }
    
    // setter
    public void setIdBarang(String idBarang) {
        this.idBarang = idBarang;
    }
    public void setNamaBarang(String namaBarang) {
        this.namaBarang = namaBarang;
    }
    public void setHarga(double harga) {
        this.harga = harga;
    }
    public void setStok(int stok) {
        this.stok = stok;
    }
    
    // function untuk menampilkan data barang
    public void displayBarang() {
        System.out.printf("%-10s %-25s Rp %,12.0f %8d unit%n", 
                         idBarang, namaBarang, harga, stok);
    }
    
    // function untuk menampilkan menu
    public static void tampilkanMenu() {
        System.out.println("\n");
        System.out.println("=====================================");
        System.out.println("  SISTEM MANAJEMEN TOKO ELEKTRONIK");
        System.out.println("=====================================");
        System.out.println("1. TAMPILKAN DATA");
        System.out.println("2. TAMBAHKAN DATA");
        System.out.println("3. UBAH DATA");
        System.out.println("4. HAPUS DATA");
        System.out.println("5. CARI DATA");
        System.out.println("6. EXIT");
        System.out.println("=====================================");
    }
    
    //function untuk menampilkan data
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
    
    // function untuk mencari index berdasarkan id
    public static int cariIndexById(String id) {
        for (int i = 0; i < dataBarang.size(); i++) {
            if (dataBarang.get(i).getIdBarang().equals(id)) {
                return i;
            }
        }
        return -1;
    }
    
    // function untuk nambah data
    public static void tambahData() {
        System.out.println("\n=== TAMBAH DATA BARANG BARU ===");
        
        System.out.print("Masukkan ID Barang: ");
        String idBarang = scanner.nextLine().trim();
        
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
        
        TokoElektronik barangBaru = new TokoElektronik(idBarang, namaBarang, harga, stok);
        dataBarang.add(barangBaru);
        System.out.println("\nData berhasil ditambahkan!");
    }
    
    // function untuk ubah data
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
        
        TokoElektronik barang = dataBarang.get(index);
        barang.setNamaBarang(namaBarang);
        barang.setHarga(harga);
        barang.setStok(stok);
        
        System.out.println("\nData berhasil diupdate!");
    }
    
    // function untuk hapus data
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
    
    // function untuk cari data
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
    
    // function untuk inisialisasi data awal
    public static void initSampleData() {
        dataBarang.add(new TokoElektronik("TV001", "43\" The Frame Samsung", 11999000, 15));
        dataBarang.add(new TokoElektronik("HP001", "iPhone 17 Pro Max", 19719000, 8));
        dataBarang.add(new TokoElektronik("LP001", "MacBook Air M4", 17999000, 12));
    }
    
    // tutup scanner
    public static void closeScanner() {
        scanner.close();
    }
}