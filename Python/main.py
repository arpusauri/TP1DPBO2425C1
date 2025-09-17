from TokoElektronik import *

def main():
    # inisialisasi data sample
    init_sample_data()
    
    while True:
        try:
            tampilkan_menu()
            pilihan = int(input("Pilih menu: "))
            
            if pilihan == 1:
                tampilkan_semua_data()
            elif pilihan == 2:
                tambah_data()
            elif pilihan == 3:
                update_data()
            elif pilihan == 4:
                hapus_data()
            elif pilihan == 5:
                cari_data()
            elif pilihan == 6:
                print("\n=== Terima kasih! Program selesai ===")
                break
            else:
                print("\nPilihan tidak valid! Silakan pilih 1-6.")
            
            if pilihan != 6:
                input("\nTekan Enter untuk kembali ke menu...")
                
        except ValueError:
            print("\nError: Masukkan angka yang valid!")
            input("Tekan Enter untuk melanjutkan...")
        except KeyboardInterrupt:
            print("\n\n=== Program dihentikan oleh user ===")
            break
        except Exception as e:
            print(f"\nTerjadi error: {e}")
            input("Tekan Enter untuk melanjutkan...")

if __name__ == "__main__":
    main()