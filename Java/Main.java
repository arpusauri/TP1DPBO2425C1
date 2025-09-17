import java.util.Scanner;
import java.util.InputMismatchException;

public class Main {
    
    // Scanner untuk input menu
    private static Scanner menuScanner = new Scanner(System.in);
    
    /**
     * Method utama untuk menjalankan program
     * @param args Command line arguments
     */
    public static void main(String[] args) {
        
        // Inisialisasi data sample
        TokoElektronik.initSampleData();
        
        int pilihan;
        
        // Main program loop
        while (true) {
            try {
                // Tampilkan menu dan ambil pilihan user
                TokoElektronik.tampilkanMenu();
                System.out.print("Pilih menu: ");
                pilihan = menuScanner.nextInt();
                menuScanner.nextLine(); // Clear buffer setelah nextInt()
                
                // Process pilihan user
                switch (pilihan) {
                    case 1:
                        TokoElektronik.tampilkanSemuaData();
                        break;
                    case 2:
                        TokoElektronik.tambahData();
                        break;
                    case 3:
                        TokoElektronik.updateData();
                        break;
                    case 4:
                        TokoElektronik.hapusData();
                        break;
                    case 5:
                        TokoElektronik.cariData();
                        break;
                    case 6:
                        System.out.println("\n=== Terima kasih! Program selesai ===");
                        // Clean up resources
                        cleanup();
                        return; // Exit program
                    default:
                        System.out.println("\nPilihan tidak valid! Silakan pilih 1-6.");
                        break;
                }
                
                // Pause sebelum kembali ke menu (kecuali exit)
                if (pilihan != 6) {
                    System.out.print("\nTekan Enter untuk kembali ke menu...");
                    menuScanner.nextLine();
                }
                
            } catch (InputMismatchException e) {
                System.out.println("\nError: Masukkan angka yang valid!");
                menuScanner.nextLine(); // Clear invalid input
                System.out.print("Tekan Enter untuk melanjutkan...");
                menuScanner.nextLine();
            } catch (Exception e) {
                System.out.println("\nTerjadi error: " + e.getMessage());
                System.out.print("Tekan Enter untuk melanjutkan...");
                menuScanner.nextLine();
            }
        }
    }
    
    /**
     * Method untuk membersihkan resources sebelum program berakhir
     */
    private static void cleanup() {
        try {
            menuScanner.close();
            TokoElektronik.closeScanner();
            System.out.println("Resources berhasil dibersihkan.");
        } catch (Exception e) {
            System.err.println("Warning: Gagal membersihkan resources - " + e.getMessage());
        }
    }
}