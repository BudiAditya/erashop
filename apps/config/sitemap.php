<?php
/**
 * Later this file will be automatically auto-generated...
 * Menu are stored in database but we create this file for faster menu creation
 */

// Load required library
require_once(LIBRARY . "node.php");

// This act as menu container
$root = new Node("[ROOT]");
$root->AddNode("HOME", "main");
$menu = $root->AddNode("PENJUALAN", null, "menu");
    $menu->AddNode("Data Relasi/Customer", null, "title");
        $menu->AddNode("Data Customer", "master.customer");
    $menu->AddNode("Transaksi Penjualan", null, "title");
        $menu->AddNode("Penjualan (POS)", "ar.invoice");
        //$menu->AddNode("Sales Order (SO)", "ar.order");
        $menu->AddNode("Penerimaan Piutang", "ar.receipt");
        $menu->AddNode("Retur Penjualan", "ar.arreturn");
        //$menu->AddNode("Pengambilan Sendiri", "fo.selfuse");
        //$menu->AddNode("Petty Cash", "cb.pettycash");
        //$menu->AddNode("Ganti Cashier", "fo.gantikasir");
    $menu->AddNode("Laporan Penjualan", null, "title");
        //$menu->AddNode("Statistik Penjualan", "report.salesstats");
        //$menu->AddNode("Daftar Sales Order", "report.salesorder");
        $menu->AddNode("Daftar Penjualan & Piutang", "ar.invoice/report");
        $menu->AddNode("Daftar Penerimaan Piutang", "ar.receipt/report");
        $menu->AddNode("Daftar Retur Penjualan", "ar.arreturn/report");
        $menu->AddNode("Laporan Profit Penjualan", "ar.invoice/profit");
$menu = $root->AddNode("PEMBELIAN", null, "menu");
    $menu->AddNode("Data Relasi/Supplier", null, "title");
        $menu->AddNode("Data Supplier", "master.supplier");
    $menu->AddNode("Transaksi Pembelian", null, "title");
        //$menu->AddNode("Pemesanan Barang", "ap.order");
        $menu->AddNode("Pembelian Barang", "ap.purchase");
        $menu->AddNode("Pembayaran Hutang", "ap.payment");
        $menu->AddNode("Retur Pembelian", "ap.apreturn");
    $menu->AddNode("Laporan Pembelian", null, "title");
        //$smenu->AddNode("Statistik Pembelian", "report.purchstats");
        //$menu->AddNode("Daftar Order Pembelian", "report.polist");
        $menu->AddNode("Daftar Pembelian", "ap.purchase/report");
        $menu->AddNode("Daftar Hutang", "ap.purchase/report");
        $menu->AddNode("Daftar Pembayaran Hutang", "ap.payment/report");
        $menu->AddNode("Daftar Retur Pembelian", "ap.apreturn/report");
$menu = $root->AddNode("INVENTORY", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Daftar Barang", "master.items");
        $menu->AddNode("Daftar Harga Barang", "master.pricelists");
        $menu->AddNode("Jenis Barang", "master.itemjenis");
        $menu->AddNode("Divisi Barang", "master.itemdivisi");
        $menu->AddNode("Kelompok Barang", "master.itemkelompok");
        $menu->AddNode("Satuan Barang", "master.itemuom");
    $menu->AddNode("Transaksi Inventory", null, "title");
        $menu->AddNode("Stock Awal Gudang", "inventory.awal");
        $menu->AddNode("Produksi & Perakitan", "inventory.assembly");
        $menu->AddNode("Stock Transfer", "inventory.transfer");
        $menu->AddNode("Stock Opname/Koreksi", "inventory.correction");
    $menu->AddNode("Laporan Inventory", null, "title");
        //$menu->AddNode("Statistik Inventory", "inventory.ivtstats");
        $menu->AddNode("Daftar Stock Barang", "inventory.stock");
        $menu->AddNode("Mutasi Stock Barang", "inventory.stock/mutasi");
        $menu->AddNode("Daftar Produksi", "inventory.assembly/report");
        $menu->AddNode("Daftar Stock Transfer", "inventory.transfer/report");
        $menu->AddNode("Daftar Stock Opname", "inventory.correction");
$menu = $root->AddNode("CASH BOOK", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Data Kas/Bank", "master.bank");
    $menu->AddNode("Transaksi Kas/Bank", null, "title");
        //$menu->AddNode("Nota Permintaan Kas/Bank", "cashbank.npkb");
        $menu->AddNode("Transaksi Warkat", "cashbank.warkat");
        $menu->AddNode("Transaksi Kas/Bank", "cashbank.cbtrx");
        //$menu->AddNode("Proses Warkat", "cashbank.warkat/proses");
    $menu->AddNode("Laporan Kas/Bank", null, "title");
        $menu->AddNode("Laporan Transaksi", "cashbank.cbtrx/report");
        $menu->AddNode("Laporan Warkat", "cashbank.warkat/report");
        //$menu->AddNode("Laporan Rek. Koran", "cashbank.cbtrx/rekoran");
$menu = $root->AddNode("AKUNTANSI", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Data Header Akun", "master.coagroup");
        $menu->AddNode("Data Akun Perkiraan", "master.coadetail");
        $menu->AddNode("Jenis Transaksi", "master.trxtype");
    $menu->AddNode("Transaksi Akuntansi", null, "title");
        $menu->AddNode("Saldo Awal Akun", "accounting.obal");
        //$menu->AddNode("Saldo Hutang", "ap.obal");
        //$menu->AddNode("Saldo Piutang", "ar.obal");
        $menu->AddNode("Set Periode Akuntansi", "main/set_periode");
        $menu->AddNode("Daftar Jurnal Akuntansi", "accounting.jurnal");
        $menu->AddNode("Print Voucher/Jurnal", "accounting.jurnal/print_all");
    $menu->AddNode("Laporan Akuntansi", null, "title");
        $subMenu = $menu->AddNode("Laporan Jurnal/Voucher", null, "submenu");
            $subMenu->AddNode("Detail", "accounting.report/journal");
            $subMenu->AddNode("Rekapitulasi", "accounting.report/recap");
        $subMenu = $menu->AddNode("Laporan Ledger", null, "submenu");
            $subMenu->AddNode("Detail", "accounting.bukutambahan/detail");
            $subMenu->AddNode("Rekapitulasi", "accounting.bukutambahan/recap");
        //$menu->AddNode("Cost & Revenue", "accounting.bukutambahan/costrevenue");
        $menu->AddNode("Trial Balance", "accounting.trialbalance/recap");
        $menu->AddNode("Worksheet Balance", "accounting.worksheetbalance/recap");
$menu = $root->AddNode("PENGATURAN", null, "menu");
    $menu->AddNode("Data Perusahaan", null, "title");
        $menu->AddNode("Data Perusahaan", "master.company");
        $menu->AddNode("Data Cabang", "master.cabang");
        $menu->AddNode("Data Bagian", "master.department");
        $menu->AddNode("Data Karyawan", "master.karyawan");
        $menu->AddNode("Setting Harga Barang", "master.setprice");
$menu->AddNode("Pemakai System", null, "title");
        $menu->AddNode("Pemakai & Hak Akses", "master.useradmin");
    $menu->AddNode("Pengaturan System", null, "title");
        $menu->AddNode("Setting Pengumuman", "master.attention");
        $menu->AddNode("Ganti Periode Transaksi", "main/set_periode");
        $menu->AddNode("Ganti Password Sendiri", "main/change_password");
        $menu->AddNode("Daftar Hak Akses", "main/aclview");
    $menu->AddNode("Lain-lain", null, "title");
        $menu->AddNode("Jenis Relasi", "master.contacttype");
// Special access for corporate
$persistence = PersistenceManager::GetInstance();
$isCorporate = $persistence->LoadState("is_corporate");
$forcePeriode = $persistence->LoadState("force_periode");
/*
if ($forcePeriode) {
	$root->AddNode("Ganti Periode", "main/set_periode");
}
$root->AddNode("Ganti Password", "main/change_password");
*/
//$root->AddNode("Notifikasi", "main");
$root->AddNode("LOGOUT", "home/logout");

// End of file: sitemap.php.php
