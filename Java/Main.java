import java.util.Scanner;
import java.util.InputMismatchException;

public class Main {
    // scanner untuk input
    private static Scanner menuScanner = new Scanner(System.in);
    
    public static void main(String[] args) {
        
        // inisialisasi data awal
        TokoElektronik.initSampleData();
        
        // int untuk pilihan
        int pilihan;
        
        // untuk pilihan menu
        while (true) {
            try {
                TokoElektronik.tampilkanMenu();
                System.out.print("Pilih menu: ");
                pilihan = menuScanner.nextInt();
                menuScanner.nextLine();
                
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
                        cleanup();
                        return;
                    default:
                        System.out.println("\nPilihan tidak valid! Silakan pilih 1-6.");
                        break;
                }
                
                if (pilihan != 6) {
                    System.out.print("\nTekan Enter untuk kembali ke menu...");
                    menuScanner.nextLine();
                }
                
            } catch (InputMismatchException e) {
                System.out.println("\nError: Masukkan angka yang valid!");
                menuScanner.nextLine();
                System.out.print("Tekan Enter untuk melanjutkan...");
                menuScanner.nextLine();
            } catch (Exception e) {
                System.out.println("\nTerjadi error: " + e.getMessage());
                System.out.print("Tekan Enter untuk melanjutkan...");
                menuScanner.nextLine();
            }
        }
    }
    
    // bersihin data
    private static void cleanup() {
        try {
            menuScanner.close();
            TokoElektronik.closeScanner();
            System.out.println("\n");
        } catch (Exception e) {
            System.err.println("Warning: Gagal membersihkan resources - " + e.getMessage());
        }
    }
}