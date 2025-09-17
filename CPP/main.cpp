#include "TokoElektronik.cpp"
using namespace std;

int main() {
    int pilihan;
    
    // Inisialisasi data sample
    initSampleData();
    
    do {
        tampilkanMenu();
        cout << "Pilih menu (1-6): ";
        cin >> pilihan;
        cin.ignore(); // Clear buffer setelah input integer
        
        switch(pilihan) {
            case 1:
                tampilkanSemuaData();
                break;
            case 2:
                tambahData();
                break;
            case 3:
                updateData();
                break;
            case 4:
                hapusData();
                break;
            case 5:
                cariData();
                break;
            case 6:
                cout << "\n=== Terima kasih! Program selesai ===" << endl;
                break;
            default:
                cout << "\nPilihan tidak valid! Silakan pilih 1-6." << endl;
        }
        
        if (pilihan != 6) {
            cout << "\nTekan Enter untuk kembali ke menu...";
            cin.get(); // Wait untuk user input
        }
        
    } while (pilihan != 6);
    
    return 0;
}