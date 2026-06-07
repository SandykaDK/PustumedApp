<?php

namespace Database\Seeders;

use App\Models\DetailPenerimaanObat;
use App\Models\NamaObat;
use App\Models\PenerimaanObat;
use App\Models\SatuanObat;
use App\Models\StokObat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaanObatSeeder2 extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $dailyUsagePatterns = [
    'Acethylsisteine capsule' => [8, 1, 8, 8, 4, 14, 13, 8, 2, 13, 13, 14, 3, 13, 7, 2, 7, 10, 1, 6, 10, 3, 14, 8, 5, 11, 14, 8, 15, 10],
    'Acyclovir 400 mg' => [10, 4, 11, 15, 3, 6, 5, 15, 6, 5, 6, 12, 11, 6, 4, 5, 11, 5, 4, 11, 10, 13, 8, 7, 14, 6, 4, 10, 7, 12],
    'Acyclovir Cream' => [9, 15, 14, 1, 12, 8, 7, 5, 12, 1, 5, 9, 9, 9, 14, 1, 10, 5, 4, 9, 10, 3, 14, 6, 14, 6, 12, 8, 2, 3],
    'Albendazole Tab' => [1, 6, 7, 4, 3, 2, 3, 3, 13, 13, 3, 2, 14, 10, 2, 15, 8, 6, 4, 9, 3, 7, 4, 9, 8, 13, 7, 4, 5, 15],
    'Allopurinol 100 mg' => [8, 4, 11, 8, 1, 11, 10, 3, 13, 9, 6, 11, 11, 1, 15, 5, 10, 9, 4, 7, 9, 6, 5, 2, 11, 13, 15, 10, 14, 2],
    'Ambroxol Tab' => [15, 5, 15, 11, 15, 10, 6, 13, 4, 12, 7, 6, 10, 2, 1, 12, 2, 2, 14, 6, 3, 9, 6, 13, 4, 15, 4, 10, 5, 3],
    'Aminophylline 200 mg' => [8, 3, 8, 7, 10, 5, 1, 14, 6, 2, 1, 4, 14, 10, 11, 12, 5, 1, 5, 6, 1, 13, 13, 5, 4, 11, 12, 12, 15, 7],
    'Aminophylline injeksi' => [13, 11, 10, 15, 14, 1, 2, 7, 5, 6, 9, 5, 1, 15, 12, 15, 11, 10, 6, 15, 5, 5, 14, 7, 9, 7, 5, 4, 10, 4],
    'Amlodipin 5 mg / 10 mg' => [13, 9, 9, 12, 6, 2, 14, 9, 10, 8, 5, 5, 14, 7, 12, 8, 12, 4, 1, 9, 12, 3, 11, 1, 10, 12, 6, 5, 3, 15],
    'Amoksisilin 500 mg' => [9, 15, 6, 14, 6, 11, 8, 2, 6, 5, 3, 2, 14, 2, 7, 11, 12, 4, 4, 10, 4, 14, 7, 15, 8, 12, 4, 4, 2, 3],
    'Amoksisilin sirup kering 125 mg/5 ml' => [6, 14, 10, 8, 12, 11, 12, 6, 2, 9, 4, 2, 4, 6, 1, 10, 14, 14, 12, 15, 7, 13, 15, 4, 13, 6, 8, 12, 8, 1],
    'Antasida DOEN Tablet Kombinasi' => [7, 15, 7, 2, 15, 15, 13, 3, 4, 2, 1, 14, 12, 6, 6, 6, 15, 7, 6, 9, 15, 11, 4, 11, 12, 2, 8, 3, 10, 4],
    'Antasida Syrup' => [8, 14, 8, 13, 14, 15, 11, 11, 3, 7, 8, 11, 12, 11, 3, 7, 11, 5, 7, 15, 14, 15, 8, 9, 4, 13, 9, 9, 8, 5],
    'Anti Bakteri Cream / Bacitrasin Cream' => [15, 13, 4, 10, 13, 7, 14, 9, 12, 7, 15, 6, 14, 3, 3, 7, 10, 5, 13, 13, 7, 9, 13, 13, 14, 3, 12, 13, 9, 11],
    'Anti Fungi Salep / Salep Whitefid' => [9, 8, 13, 2, 5, 15, 6, 11, 14, 15, 15, 12, 13, 6, 14, 9, 7, 8, 14, 2, 13, 2, 3, 15, 3, 4, 4, 1, 15, 11],
    'Anti Hemoroid Supp' => [7, 7, 1, 7, 13, 3, 14, 8, 9, 15, 1, 10, 13, 9, 9, 1, 6, 12, 9, 14, 10, 14, 5, 15, 14, 3, 5, 2, 5, 7],
    'Asam Acetyl Selisilat 100 mg' => [4, 5, 14, 8, 9, 10, 12, 2, 13, 8, 10, 4, 15, 3, 9, 12, 4, 2, 3, 7, 7, 5, 3, 15, 4, 7, 8, 3, 5, 2],
    'Asam Askorbat (Vit.C) tablet 50 mg' => [7, 3, 9, 13, 8, 3, 3, 1, 13, 3, 5, 6, 8, 5, 14, 8, 2, 2, 6, 13, 2, 6, 10, 15, 10, 3, 2, 8, 11, 10],
    'Asam Mefenamat 500 mg' => [3, 9, 14, 4, 4, 13, 12, 5, 4, 10, 8, 7, 14, 10, 6, 3, 10, 9, 6, 9, 12, 9, 12, 14, 6, 15, 2, 13, 9, 5],
    'Betametason krim 0,1%' => [4, 10, 5, 2, 3, 2, 4, 13, 10, 13, 7, 4, 4, 2, 7, 10, 14, 3, 9, 4, 8, 9, 13, 13, 14, 7, 7, 6, 1, 15],
    'Bromhexine Tab' => [7, 10, 8, 14, 15, 11, 7, 13, 10, 12, 12, 2, 2, 8, 10, 1, 8, 9, 11, 5, 14, 3, 12, 12, 7, 4, 11, 13, 5, 13],
    'Deksametason Tablet 0,5 mg' => [3, 2, 12, 10, 15, 6, 2, 3, 13, 15, 15, 6, 6, 6, 12, 1, 11, 11, 2, 8, 15, 1, 1, 2, 7, 12, 13, 5, 3, 14],
    'Deksametason injeksi' => [5, 5, 8, 9, 3, 8, 3, 15, 13, 1, 8, 8, 2, 2, 3, 3, 2, 4, 4, 10, 10, 15, 2, 12, 6, 13, 14, 7, 4, 2],
    'Difenhidramin HCI inj. 10 mg/ml - 1 ml' => [7, 8, 1, 15, 6, 3, 15, 15, 3, 5, 10, 14, 9, 14, 4, 15, 6, 14, 14, 11, 4, 10, 7, 8, 3, 13, 11, 14, 6, 2],
    'Dimenhidrinat 50 mg' => [13, 3, 6, 1, 12, 12, 6, 13, 12, 9, 10, 7, 3, 8, 11, 3, 11, 5, 4, 1, 6, 2, 5, 4, 6, 12, 12, 9, 9, 1],
    'Domperidone' => [10, 4, 2, 1, 14, 11, 4, 11, 7, 7, 9, 2, 1, 15, 11, 6, 3, 12, 15, 14, 13, 13, 7, 13, 2, 10, 14, 11, 8, 4],
    'Erytromysin 500 mg' => [12, 2, 4, 14, 2, 15, 4, 11, 9, 13, 2, 14, 15, 1, 15, 14, 7, 10, 9, 2, 13, 15, 4, 5, 6, 13, 8, 6, 11, 6],
    'Etanol 70%' => [1, 3, 6, 5, 6, 10, 14, 9, 3, 5, 12, 13, 2, 7, 3, 8, 2, 12, 15, 8, 10, 3, 8, 12, 15, 10, 3, 8, 6, 6],
    'Fenol Gliseron Tetestelinga 10%' => [2, 8, 4, 13, 8, 5, 1, 13, 2, 15, 9, 9, 14, 14, 5, 15, 7, 5, 9, 10, 9, 2, 15, 8, 14, 8, 6, 6, 13, 9],
    'Fitomenadion (Vit.K1) injeksi 10 mg/ml - 1 ml' => [3, 12, 14, 8, 14, 9, 7, 14, 9, 14, 10, 14, 3, 15, 13, 1, 6, 2, 9, 1, 13, 1, 4, 3, 11, 8, 5, 13, 12, 1],
    'Fitomenadion (Vit.K1) tablet salut gula 10 mg' => [5, 8, 6, 13, 15, 12, 4, 5, 9, 1, 7, 8, 15, 5, 15, 15, 13, 14, 1, 13, 5, 9, 6, 2, 7, 7, 12, 7, 13, 4],
    'Furosemid Tablet 40 mg' => [12, 2, 2, 14, 12, 8, 4, 13, 5, 7, 5, 4, 9, 2, 8, 3, 8, 5, 10, 14, 12, 15, 13, 12, 10, 12, 5, 11, 10, 8],
    'Garam Oralit untuk 200 ml air' => [14, 10, 11, 6, 5, 7, 13, 12, 8, 14, 6, 7, 1, 15, 1, 6, 10, 14, 11, 13, 3, 10, 15, 4, 10, 5, 7, 9, 6, 14],
    'Glibenklamide 5 mg' => [8, 14, 3, 14, 8, 6, 9, 14, 10, 13, 9, 6, 10, 10, 13, 15, 6, 11, 7, 11, 1, 6, 3, 1, 2, 14, 6, 4, 1, 8],
    'Glimepiride 1 mg / 2 mg' => [10, 15, 5, 5, 11, 6, 12, 12, 4, 13, 6, 2, 6, 14, 7, 13, 14, 7, 8, 8, 10, 8, 8, 15, 3, 12, 1, 5, 3, 8],
    'Gliseril Guayakolat tablet 100 mg' => [10, 4, 15, 5, 1, 3, 4, 11, 6, 14, 9, 10, 2, 8, 1, 13, 2, 11, 8, 2, 9, 6, 6, 1, 7, 4, 10, 2, 11, 6],
    'Glukosa Larutan Infus 5% Steril (Produk Lokal)' => [6, 11, 9, 11, 11, 6, 14, 6, 10, 9, 14, 14, 10, 8, 11, 1, 11, 12, 11, 4, 8, 14, 4, 8, 5, 6, 5, 6, 5, 8],
    'Haloperidol tablet 5 mg' => [14, 14, 1, 5, 1, 14, 2, 2, 3, 15, 5, 1, 7, 5, 12, 12, 14, 5, 6, 8, 9, 7, 1, 11, 2, 15, 12, 13, 9, 3],
    'Hidrokortison krim 2,5%' => [13, 12, 6, 2, 6, 6, 11, 8, 2, 7, 7, 5, 7, 10, 1, 12, 11, 13, 11, 10, 9, 5, 12, 7, 14, 4, 7, 12, 6, 13],
    'Ibuproden tablet 200mg / 400 mg' => [9, 10, 7, 14, 1, 13, 8, 15, 6, 9, 7, 10, 5, 1, 2, 13, 7, 6, 7, 14, 3, 4, 7, 9, 10, 5, 7, 1, 14, 9],
    'Isosorbid Dinitrat Tablet Sublingual 5 mg' => [7, 15, 12, 9, 9, 3, 12, 10, 10, 5, 5, 2, 12, 2, 5, 1, 8, 13, 5, 15, 14, 12, 12, 6, 7, 6, 8, 2, 2, 14],
    'Kalsium Laktat (Kalk) Tablet 500 mg' => [2, 4, 7, 2, 15, 5, 5, 3, 12, 11, 10, 9, 14, 2, 14, 1, 4, 7, 15, 3, 13, 1, 15, 8, 4, 2, 9, 1, 6, 12],
    'Kaptopril 12,5 mg / 25 mg' => [2, 5, 14, 5, 15, 13, 10, 13, 2, 12, 15, 11, 14, 6, 8, 8, 1, 2, 14, 7, 4, 5, 4, 12, 10, 12, 8, 9, 9, 1],
    'Ketokonazol 200 mg' => [4, 11, 14, 11, 7, 9, 15, 6, 11, 14, 12, 10, 6, 10, 10, 4, 4, 2, 13, 11, 14, 5, 14, 15, 14, 5, 1, 15, 13, 8],
    'Kloramfenikol kapsul 250 mg/ 500 mg' => [4, 1, 6, 6, 11, 2, 9, 10, 1, 13, 11, 4, 3, 5, 6, 9, 8, 10, 2, 6, 11, 7, 12, 12, 10, 3, 13, 14, 7, 9],
    'Kloramfenikol Syrup' => [8, 6, 15, 3, 1, 6, 12, 12, 9, 2, 15, 11, 14, 15, 2, 14, 4, 9, 1, 8, 8, 8, 6, 10, 12, 6, 7, 7, 9, 3],
    'Kloramfenikol Tetes Mata 0,5%' => [1, 1, 3, 12, 3, 7, 3, 13, 12, 4, 15, 13, 1, 9, 8, 15, 13, 2, 7, 10, 6, 10, 8, 8, 13, 5, 7, 14, 7, 8],
    'Kloramfenikol Tetes Telinga 3%' => [10, 8, 15, 4, 8, 13, 12, 6, 15, 13, 7, 11, 3, 12, 13, 12, 14, 8, 14, 8, 7, 8, 7, 9, 11, 8, 15, 7, 10, 10],
    'Klorfeniramin Maleat (CTM) tablet 4 mg' => [13, 5, 7, 9, 2, 8, 7, 3, 15, 13, 12, 10, 12, 11, 7, 5, 3, 3, 14, 5, 2, 13, 3, 2, 5, 15, 12, 10, 13, 6],
    'Klorpromazin HCI Tablet Salut 100 mg' => [14, 12, 11, 8, 10, 11, 15, 7, 13, 13, 11, 12, 3, 4, 9, 7, 4, 2, 14, 13, 13, 1, 14, 15, 11, 8, 14, 1, 6, 5],
    'Kotrimoksazol tab Dewasa 480 mg / 960 mg' => [7, 1, 14, 5, 8, 5, 11, 1, 12, 13, 6, 14, 1, 3, 6, 12, 7, 1, 13, 13, 8, 14, 2, 2, 6, 2, 5, 8, 12, 14],
    'Lidokain Komp. Injeksi' => [11, 10, 10, 10, 12, 5, 14, 13, 1, 15, 6, 11, 13, 10, 14, 3, 15, 6, 3, 9, 4, 6, 10, 1, 6, 9, 15, 13, 10, 13],
    'Loratadin' => [12, 5, 9, 6, 2, 5, 14, 15, 7, 5, 9, 1, 14, 2, 8, 15, 10, 10, 12, 7, 2, 15, 10, 12, 3, 13, 14, 5, 7, 3],
    'Metamphyron / antalgin 500 mg' => [2, 13, 15, 6, 9, 4, 1, 9, 1, 10, 9, 11, 8, 14, 13, 2, 12, 13, 12, 6, 10, 12, 14, 6, 14, 10, 4, 10, 2, 4],
    'Metoclopramid Tab' => [9, 13, 12, 14, 15, 8, 8, 13, 2, 14, 15, 3, 9, 8, 13, 15, 4, 1, 5, 5, 13, 5, 4, 1, 3, 6, 9, 5, 10, 14],
    'Metformin 500 mg' => [5, 1, 14, 9, 11, 9, 3, 12, 14, 13, 9, 15, 10, 12, 7, 11, 13, 14, 1, 1, 1, 10, 8, 2, 10, 10, 6, 5, 14, 14],
    'Metilergometrin Maleat Tablet Salut 0,125 mg' => [7, 10, 15, 14, 11, 15, 12, 8, 14, 9, 6, 1, 12, 6, 13, 13, 15, 7, 6, 15, 12, 5, 5, 2, 10, 1, 13, 13, 6, 11],
    'Metilergometrin Maleat tinj. 0,200 mg - 1 ml' => [14, 14, 13, 7, 10, 8, 14, 5, 4, 14, 5, 9, 12, 14, 6, 3, 4, 13, 4, 9, 12, 14, 14, 10, 14, 15, 2, 14, 12, 12],
    'Metronidazol tablet 500 mg' => [8, 5, 11, 5, 4, 13, 13, 11, 2, 15, 8, 8, 7, 12, 11, 13, 1, 10, 4, 13, 3, 15, 10, 8, 7, 4, 3, 13, 11, 14],
    'Mikonazol Cream' => [10, 9, 5, 13, 15, 9, 2, 7, 14, 1, 14, 14, 1, 8, 2, 4, 14, 4, 4, 5, 3, 15, 1, 4, 5, 11, 2, 12, 2, 9],
    'Natrium Diklofenac 50 mg' => [8, 4, 15, 10, 1, 5, 13, 7, 9, 15, 13, 10, 14, 12, 10, 10, 12, 5, 13, 14, 13, 10, 4, 7, 7, 2, 11, 2, 1, 14],
    'Natrium Clorida Infus (Pz)' => [15, 1, 6, 4, 4, 10, 2, 2, 3, 1, 13, 14, 14, 1, 10, 6, 5, 6, 4, 10, 9, 10, 13, 9, 5, 2, 12, 3, 4, 5],
    'Nistatin Vaginal Tablet' => [10, 7, 1, 1, 14, 9, 13, 12, 10, 3, 12, 5, 5, 2, 13, 7, 7, 13, 7, 12, 6, 2, 3, 13, 3, 3, 13, 9, 8, 6],
    'Obat batuk Hitam (O.B.H) Cairan' => [5, 14, 13, 14, 8, 13, 2, 1, 11, 12, 3, 6, 15, 11, 11, 5, 14, 5, 2, 9, 13, 15, 10, 12, 2, 5, 14, 12, 15, 3],
    'Oksitetrasiklin HCI Salep Kulit' => [6, 14, 7, 12, 13, 4, 3, 10, 7, 8, 12, 1, 11, 12, 2, 4, 5, 3, 4, 10, 13, 15, 12, 13, 12, 8, 12, 7, 8, 11],
    'Oksitetrasiklin HCI Salep Mata 1%' => [9, 7, 4, 10, 11, 11, 4, 4, 5, 12, 3, 9, 7, 5, 15, 5, 9, 4, 3, 10, 13, 2, 14, 4, 15, 8, 4, 13, 9, 7],
    'Oksitetrasiklin Injeksi 10 IU/ml - 1 ml' => [1, 3, 13, 5, 4, 2, 4, 4, 6, 14, 13, 4, 13, 8, 11, 15, 1, 5, 5, 5, 4, 2, 7, 8, 8, 13, 9, 11, 10, 3],
    'Parasetamol Sirup 120 mg / 5 ml' => [13, 8, 8, 9, 5, 7, 1, 15, 13, 5, 1, 6, 11, 9, 10, 9, 1, 3, 5, 6, 4, 13, 9, 6, 7, 9, 2, 2, 12, 8],
    'Parasetamol 500 mg' => [13, 5, 15, 12, 10, 8, 8, 3, 2, 5, 1, 15, 4, 9, 10, 8, 3, 12, 5, 4, 12, 10, 13, 13, 3, 5, 14, 7, 15, 13],
    'Piridoksin HCL (Vit.B 6) Tablet 10 mg' => [7, 4, 15, 10, 4, 13, 3, 7, 5, 4, 4, 4, 13, 8, 8, 12, 2, 7, 6, 13, 14, 13, 10, 10, 8, 9, 2, 6, 9, 4],
    'Povidon Iodida 10% 30 ml / 60 ml' => [12, 13, 5, 6, 10, 9, 9, 5, 12, 14, 7, 8, 2, 10, 10, 9, 13, 11, 2, 9, 2, 10, 6, 5, 10, 9, 5, 13, 7, 13],
    'Povidon iodida 10% 300 ml' => [7, 3, 3, 5, 7, 8, 6, 1, 12, 6, 2, 14, 6, 7, 13, 13, 9, 15, 8, 2, 1, 12, 11, 5, 14, 1, 7, 11, 7, 8],
    'Presnison Tablet 5 mg' => [14, 4, 11, 1, 9, 10, 14, 1, 1, 13, 4, 7, 15, 11, 11, 11, 5, 12, 5, 3, 2, 6, 12, 4, 2, 13, 8, 6, 14, 5],
    'Ranitidine 150 mg tab' => [4, 8, 13, 1, 8, 6, 8, 8, 11, 8, 11, 3, 13, 1, 11, 1, 11, 2, 6, 3, 2, 4, 6, 14, 11, 10, 15, 9, 4, 13],
    'Ranitidine Injeksi' => [13, 9, 15, 2, 1, 12, 2, 10, 13, 14, 10, 10, 13, 14, 6, 7, 3, 10, 9, 12, 5, 1, 9, 12, 9, 3, 5, 15, 13, 1],
    'Ringer Laktat Larutan Infus Steril (Produk Lokal)' => [3, 2, 9, 9, 6, 2, 8, 10, 14, 4, 5, 1, 14, 10, 5, 12, 14, 15, 7, 13, 2, 6, 15, 14, 8, 5, 13, 8, 3, 5],
    'Salbutamol 2 mg' => [3, 6, 5, 1, 15, 14, 13, 4, 12, 5, 8, 3, 12, 12, 12, 12, 3, 8, 6, 9, 8, 2, 12, 3, 8, 8, 12, 3, 7, 4],
    'Salep 2-4, Asam Salisilat 2% + Belerangendap...' => [9, 8, 13, 10, 1, 7, 15, 4, 15, 11, 10, 8, 2, 15, 8, 4, 10, 2, 10, 1, 5, 9, 4, 5, 9, 14, 13, 3, 7, 15],
    'Salisil Bedak 2%' => [15, 3, 3, 4, 15, 2, 7, 7, 13, 7, 11, 13, 14, 1, 6, 10, 1, 11, 13, 8, 1, 1, 13, 10, 1, 6, 5, 11, 7, 2],
    'Sianokobalamin (Vit. B 12) Injeksi 500mg - m...' => [13, 9, 11, 11, 1, 11, 3, 4, 6, 4, 13, 14, 5, 7, 4, 8, 4, 11, 14, 6, 1, 6, 6, 15, 7, 13, 6, 12, 10, 2],
    'Simvastain 10 mg' => [2, 5, 6, 13, 15, 10, 15, 12, 15, 8, 11, 11, 7, 5, 14, 5, 4, 3, 7, 5, 6, 2, 13, 4, 11, 6, 15, 1, 11, 4],
    'Siprofoksasin 500 mg' => [10, 4, 11, 10, 10, 13, 8, 15, 2, 14, 5, 3, 15, 14, 15, 9, 1, 5, 6, 10, 1, 12, 3, 10, 2, 12, 14, 4, 14, 1],
    'Tetrasiklin HCI Kapsul 250 mg / 500 mg' => [13, 8, 14, 13, 2, 1, 11, 14, 9, 15, 11, 11, 5, 7, 2, 12, 9, 7, 13, 8, 4, 15, 12, 1, 9, 4, 1, 14, 13, 13],
    'Thiamphenikol 500 mg' => [11, 2, 13, 12, 7, 5, 15, 5, 4, 6, 5, 9, 13, 1, 4, 8, 3, 6, 4, 15, 7, 8, 14, 14, 2, 13, 5, 13, 2, 6],
    'Tiamin HCI / Mononitrat (Vit. B 1) Tablet 50...' => [14, 14, 12, 12, 11, 11, 9, 12, 5, 9, 7, 9, 4, 15, 5, 13, 2, 5, 13, 12, 13, 11, 14, 8, 7, 15, 2, 3, 13, 1],
    'Triheksilfenidil Hidroklorida Tablet 2 mg' => [1, 6, 9, 8, 3, 8, 15, 9, 3, 1, 2, 9, 9, 7, 1, 15, 9, 9, 10, 4, 14, 15, 15, 9, 14, 3, 4, 11, 3, 2],
    'Vitamin B Komplex Tablet' => [4, 1, 9, 5, 4, 15, 3, 13, 12, 11, 15, 4, 9, 11, 8, 2, 2, 11, 5, 9, 3, 2, 9, 7, 13, 14, 1, 2, 3, 13],
    'Zinc Tablet' => [6, 2, 5, 5, 6, 8, 5, 6, 10, 12, 4, 2, 15, 11, 14, 15, 12, 15, 4, 3, 8, 1, 10, 5, 7, 8, 10, 9, 2, 11],
    'Attapulgit' => [10, 7, 2, 9, 7, 14, 1, 3, 14, 4, 2, 7, 6, 13, 2, 9, 4, 15, 1, 15, 13, 2, 11, 12, 2, 1, 7, 7, 9, 1],
    'Perhidrol' => [11, 5, 4, 6, 15, 4, 11, 15, 5, 15, 10, 15, 12, 7, 15, 4, 6, 6, 11, 10, 11, 10, 1, 1, 1, 10, 6, 12, 15, 14],
    'Alkohol Swab' => [8, 14, 5, 9, 6, 14, 5, 1, 15, 6, 7, 6, 1, 6, 9, 3, 14, 3, 12, 8, 12, 2, 15, 3, 11, 13, 14, 1, 14, 9],
    'Kapas 250 gram' => [10, 11, 4, 9, 6, 3, 11, 8, 2, 8, 9, 7, 13, 14, 10, 6, 9, 12, 10, 9, 13, 8, 7, 7, 4, 8, 15, 5, 3, 4],
    'Kasa Hidrofil 16 cm x 16 cm' => [11, 12, 15, 1, 10, 3, 2, 12, 4, 6, 4, 9, 4, 14, 2, 15, 4, 3, 4, 2, 7, 13, 1, 14, 3, 4, 6, 9, 1, 15],
    'Kasa Kompress 40 x 40 Steril' => [3, 14, 10, 8, 1, 2, 12, 4, 8, 10, 13, 6, 10, 14, 6, 5, 3, 12, 9, 6, 2, 3, 9, 11, 14, 3, 1, 2, 1, 5],
    'Kasa Pembalut 2 m x 80 cm' => [10, 15, 5, 1, 7, 4, 11, 12, 14, 13, 1, 12, 9, 7, 9, 4, 5, 12, 12, 11, 11, 1, 3, 1, 3, 6, 10, 14, 9, 15],
    'Kasa Pembalut 4 m x 15 cm' => [5, 2, 1, 14, 4, 13, 15, 8, 10, 9, 6, 7, 15, 8, 6, 9, 10, 4, 12, 4, 6, 8, 1, 10, 5, 10, 1, 6, 14, 9],
    'Kasa Pembalut 4 m x 3 cm' => [3, 2, 14, 10, 6, 6, 15, 14, 9, 14, 1, 6, 15, 15, 12, 13, 10, 8, 3, 15, 10, 3, 6, 9, 7, 14, 7, 7, 12, 11],
    'Plester Coklat / Putih' => [1, 10, 9, 2, 14, 2, 15, 9, 11, 12, 2, 3, 2, 6, 14, 8, 2, 11, 3, 14, 12, 12, 3, 2, 14, 1, 6, 7, 7, 8],
            ];

            $namaObatMap = NamaObat::whereIn('nama_obat', array_keys($dailyUsagePatterns))
                ->get()
                ->keyBy('nama_obat');

            $userIds = User::pluck('id')->take(4)->all();
            $lokasiPenyimpanan = ['Rak A1', 'Rak A2', 'Rak A3', 'Rak A4', 'Rak B1', 'Rak B2', 'Rak B3', 'Rak B4', 'Rak C1', 'Rak C2', 'Rak C3', 'Rak C4', 'Rak D1', 'Rak D2', 'Rak D3'];

            if (empty($userIds) || $namaObatMap->isEmpty()) {
                return;
            }

            $startDate = Carbon::create(2026, 1, 1);
            $endDate = Carbon::create(2026, 6, 30);
            $monthlyTotals = [];
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                $monthKey = $date->format('Y-m');
                $dayIndex = $date->day - 1;

                foreach ($dailyUsagePatterns as $namaObatNama => $pattern) {
                    $monthlyTotals[$monthKey][$namaObatNama] = ($monthlyTotals[$monthKey][$namaObatNama] ?? 0)
                        + $pattern[$dayIndex % count($pattern)];
                }

                $date->addDay();
            }

            $monthDate = Carbon::create(2026, 1, 1);

            while ($monthDate->lte(Carbon::create(2026, 6, 1))) {
                $monthKey = $monthDate->format('Y-m');
                $penerimaan = PenerimaanObat::create([
                    'no_batch' => 'BATCH-' . $monthDate->format('Ym') . '-001',
                    'tanggal_penerimaan' => $monthDate->format('Y-m-d'),
                    'user_id' => $userIds[($monthDate->month - 1) % count($userIds)],
                    'keterangan' => 'Penerimaan stok bulan ' . $monthDate->format('F Y') . ' untuk kebutuhan pengeluaran obat.',
                ]);

                $detailIndex = 0;

                foreach ($dailyUsagePatterns as $namaObatNama => $pattern) {
                    $namaObat = $namaObatMap->get($namaObatNama);
                    if (! $namaObat) {
                        continue;
                    }

                    $monthlyTotal = $monthlyTotals[$monthKey][$namaObatNama] ?? 0;
                    $jumlahMasuk = max($monthlyTotal + 120, 300);
                    $detailIndex++;
                    $batchCode = 'BATCH-' . $monthDate->format('Ym') . '-' . str_pad($detailIndex, 3, '0', STR_PAD_LEFT);
                    $satuanId = $namaObat->satuan_obat_id ?? SatuanObat::first()?->id;

                    DetailPenerimaanObat::create([
                        'penerimaan_obat_id' => $penerimaan->id,
                        'nama_obat_id' => $namaObat->id,
                        'jenis_obat_id' => $namaObat->jenis_obat_id ?? 1,
                        'no_batch' => $batchCode,
                        'tanggal_kadaluwarsa' => $monthDate->copy()->addYear()->format('Y-m-d'),
                        'jumlah_masuk' => $jumlahMasuk,
                        'satuan_id' => $satuanId,
                        'lokasi_penyimpanan' => $lokasiPenyimpanan[array_rand($lokasiPenyimpanan)],
                    ]);

                    StokObat::create([
                        'nama_obat_id' => $namaObat->id,
                        'tanggal_kadaluwarsa' => $monthDate->copy()->addYear()->format('Y-m-d'),
                        'stok' => $jumlahMasuk,
                        'no_batch' => $batchCode,
                        'keterangan' => 'Stok awal penerimaan bulan ' . $monthDate->format('F Y') . ' seeder.',
                    ]);
                }

                $monthDate->addMonth();
            }
        });
    }
}
