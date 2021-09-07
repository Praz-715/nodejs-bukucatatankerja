const xlsx = require('xlsx');
const path = require('path');

const exportExcel = (data, workSheetColumnNames, workSheetName, filePath) => {
    const workBook = xlsx.utils.book_new();
    const workSheetData = [
        workSheetColumnNames,
        ...data
    ];

    const workSheet = xlsx.utils.aoa_to_sheet(workSheetData);
    xlsx.utils.book_append_sheet(workBook, workSheet, workSheetName);
    xlsx.writeFile(workBook, path.resolve(filePath));
}

const exportCatatanKerjaToExcel = (catatanKerja, workSheetColumnNames, workSheetName, filePath) => {
    const data = catatanKerja.map(catatan => {
        return [
            catatan.lokasi,
            catatan.tipeaset,
            catatan.sn,
            catatan.status,
            catatan.keterangan.join(', '),
            catatan.time.toLocaleDateString()
        ]
    });

    exportExcel(data, workSheetColumnNames, workSheetName, filePath);
}

module.exports = exportCatatanKerjaToExcel;