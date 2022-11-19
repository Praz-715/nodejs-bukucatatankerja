const express = require('express');
const expressLayouts = require('express-ejs-layouts');
const { body, validationResult, check } = require('express-validator');
const methodOverride = require('method-override');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const flash = require('connect-flash');



const exportCatatanKerjaToExcel = require('./utils/exportService');

const app = express();
const port = parseInt(process.env.PORT) || 3000;

// Setup connection DB
require('./utils/db');
const Report = require('./models/report');
const AssetKerja = require('./models/assetkerja');
const Lokasi = require('./models/lokasi');

// SetUp EJS
app.set('view engine', 'ejs');
app.use(expressLayouts);
app.use(express.static('public'));
app.use(express.urlencoded({ extended: true }));
app.use('/favicon.ico', express.static('images/favicon.ico'));

// SetUp Flash
app.use(cookieParser('secret'));
app.use(session({
    cookie: { maxAge: 6000 },
    secret: 'secret',
    resave: true,
    saveUninitialized: true,
}));
app.use(flash());

// override with the X-HTTP-Method-Override header in the request
app.use(methodOverride('_method'));


app.post('/add', (req, res) => {
    const lokasi = req.body.lokasi.toUpperCase();
    const tipeaset = req.body.tipeaset.toUpperCase();
    const sn = req.body.sn.toUpperCase();
    const status = req.body.status.toUpperCase();
    let keterangan = [];

    switch (status) {
        case 'OK':
            const instalok = req.body.instalok;
            if (instalok) {
                keterangan.push(instalok.toUpperCase())
            }
            if (req.body.keteranganok.length > 0) {
                keterangan.push(req.body.keteranganok.toUpperCase());
            }
            break;
        case 'RUSAK':
            const mbrusak = req.body.mbrusak;
            if (mbrusak) {
                keterangan.push(mbrusak.toUpperCase())
            }
            if (req.body.keteranganrusak.length > 0) {
                keterangan.push(req.body.keteranganrusak.toUpperCase());
            }
            break;
        case 'SUPPLIER':
            keterangan.push(req.body.keterangansupplier.toUpperCase());
            keterangan.push("supplier " + req.body.namasupplier.toUpperCase());
            break;
        case 'IR':
            if (Array.isArray(req.body.ir)) {
                keterangan.push(...req.body.ir);
            } else {
                keterangan.push(req.body.ir);
            }
            break;
    }
    const tanggal = new Date;
    const upload = tanggal.toISOString().split('T')[0];

    AssetKerja.insertMany({ lokasi, tipeaset, sn, status, keterangan, upload }, (error, result) => {
            // sebelum redirect kirim flash
            req.flash('msg', `${lokasi} SN ${sn} ${status} berhasil ditambahkan`);
            res.redirect('/add');
        })
        // res.json(req.body);
});

app.get('/add', (req, res) => {
    // console.log(req);
    res.render('index', { layout: 'layouts/main-layout', title: 'Tambah data', msg: req.flash('msg') })
});

app.delete('/edit', (req, res) => {
    const id = req.body.id;

    AssetKerja.findOneAndRemove({ _id: id }, (err, docs) => {
        if (err) {
            console.log('Gagal dihapus', err)
        } else {
            console.log('Berhasil dihapus')
            req.flash('edit', 'Data berhasil dihapus');
            res.redirect('/');
        }
    });
})

app.post('/edit', (req, res) => {
    const id = req.body.id;
    const lokasi = req.body.lokasi.toUpperCase();
    const tipeaset = req.body.tipeaset.toUpperCase();
    const sn = req.body.sn.toUpperCase();
    const status = req.body.status.toUpperCase();
    let keterangan = [];

    switch (status) {
        case 'OK':
            const instalok = req.body.instalok;
            if (instalok) {
                keterangan.push(instalok.toUpperCase())
            }
            if (req.body.keteranganok.length > 0) {
                keterangan.push(req.body.keteranganok.toUpperCase());
            }
            break;
        case 'RUSAK':
            const mbrusak = req.body.mbrusak;
            if (mbrusak) {
                keterangan.push(mbrusak.toUpperCase())
            }
            if (req.body.keteranganrusak.length > 0) {
                keterangan.push(req.body.keteranganrusak.toUpperCase());
            }
            break;
        case 'SUPPLIER':
            keterangan.push(req.body.keterangansupplier.toUpperCase());
            keterangan.push("supplier " + req.body.namasupplier.toUpperCase());
            break;
        case 'IR':
            if (Array.isArray(req.body.ir)) {
                keterangan.push(...req.body.ir);
            } else {
                keterangan.push(req.body.ir);
            }
            break;
    }
    const tanggal = new Date;
    const update = tanggal.toISOString().split('T')[0];

    AssetKerja.findByIdAndUpdate(id, {
            $set: {
                lokasi,
                tipeaset,
                sn,
                status,
                keterangan,
                update
            }
        },
        (err, docs) => {
            if (err) {
                console.log(err)
            } else {
                // console.log("ini edit post", docs);
                req.flash('edit', 'Data berhasil diubah');
                res.redirect('/');
            }
        })
});

app.get('/edit', async(req, res) => {
    const data = await AssetKerja.findById(req.query.id);
    res.render('edit', { layout: 'layouts/main-layout', title: 'Ubah data', msg: req.flash('msg'), data })
});

app.post('/export', async(req, res) => {
    const excelOrPrint = req.body.excel || req.body.print;
    const cekArray = (arrayOrString) => Array.isArray(arrayOrString) ? arrayOrString : [arrayOrString];
    const pilihan = cekArray(req.body.pilihan);
    const opsi = req.body.opsi;
    let optionsFilter = { status: { $in: pilihan } };
    let opsinya;


    const hariini = new Date();

    switch (opsi) {
        case 'hariini':
            optionsFilter.upload = hariini.toISOString().split('T')[0];
            opsinya = `per tanggal ${hariini.toLocaleDateString('id-ID').split(' ')[0].replace('/','-').replace('/','-')}`;
            break;
        case 'bulanini':
            optionsFilter.time = {
                $gte: new Date(hariini.getFullYear(), hariini.getMonth(), 1),
                $lte: new Date(hariini.getFullYear(), hariini.getMonth() + 1, 1)
            }
            opsinya = `per bulan ${hariini.toLocaleString('id-ID', { month: 'long', year: 'numeric' })}`;
            break;
        case 'tahunini':
            optionsFilter.time = {
                $gte: new Date(hariini.getFullYear(), 0, 1),
                $lte: new Date(hariini.getFullYear() + 1, 0, 1)
            }
            opsinya = `per tahun ${hariini.toLocaleString('id-ID', { year: 'numeric' })}`;
            break;
        case 'pertanggal':
            const dari = new Date(req.body.dari);
            const sampai = new Date(req.body.sampai);
            optionsFilter.time = {
                $gte: new Date(dari),
                $lte: new Date(sampai.getFullYear(), sampai.getMonth(), sampai.getDate() + 1)
            }
            opsinya = `per dari ${dari.toLocaleDateString('id-ID').split(' ')[0].replace('/','-').replace('/','-')} sampai ${sampai.toLocaleDateString('id-ID').split(' ')[0].replace('/','-').replace('/','-')}`;
            break;
        default:
            res.status(404).send(404);
    }

    switch (excelOrPrint) {
        case 'excel':
            const data = await AssetKerja.find(optionsFilter);
            const workSheetColumnNames = ['Lokasi', 'Tipe Aset', 'Serial Number', 'Status', 'Keterangan', 'Tanggal'];
            const workSheetName = 'CatatanKerja';
            const pesan = `${hariini.toLocaleString('id-ID').replace('/','-').replace('/','-')} - Catatan kerja ${pilihan} - ${opsinya}`;
            const filePath = './outputFiles//' + pesan + '.xlsx';
            exportCatatanKerjaToExcel(data, workSheetColumnNames, workSheetName, filePath);
            Report.insertMany({ name: pesan, filePath: './outputFiles/' + pesan + '.xlsx' }, (error, result) => {
                res.download('./outputFiles/' + pesan + '.xlsx');
            })
            break;
        case 'print':
            break;
        default:
            res.status(404).send("<h1>404</h1>");
    }

    // console.log(optionsFilter)
    // res.send(data)

});

app.get('/lokasi', async(req, res) => {

    // Inserting
    // const fileBuffer = fs.readFileSync('./models/lokasi.json', 'utf-8');
    // const insert = JSON.parse(fileBuffer);

    // insert.forEach(element => {
    //     console.log("foreeach");
    //     Lokasi.insertMany(element, (err, info) => {
    //         if (err) {
    //             console.log(err)
    //         }
    //     })
    // });

    const data = await Lokasi.find();
    res.render('lokasi', { layout: 'layouts/main-layout', title: 'Laporan', data });

});

app.get('/report', (req, res) => {

});

app.get('/reports', async(req, res) => {
    reports = await Report.find();
    res.render('reports', { layout: 'layouts/main-layout', title: 'Laporan', reports });

});

app.get('/', async(req, res) => {

    // bikin fungsi untuk besoknya
    const dateTomorrow = (date) => {
        const sekarang = new Date(date)
        sekarang.setDate(sekarang.getDate() + 1);
        return sekarang.toISOString().split('T')[0];
    };

    // Ambil tanggal untuk pencarian
    // const tanggal = await AssetKerja.distinct('upload');
    let tanggal = await AssetKerja.find({}, 'upload');
    tanggal = new Set(tanggal.map(e=>e.upload))
    tanggal = Array.from(tanggal)
    tanggal.reverse();
    // console.log(tanggal)

    // Opsi tanggal kedua
    // const ambilTanggal = await AssetKerja.aggregate([
    //     { $group: { _id: '$upload' } },
    //     { $limit: 5 },
    //     { $sort: { upload: 1 } }
    // ]);
    // let tanggal = [];
    // ambilTanggal.forEach((e) => {
    //     tanggal.push(...Object.values(e));
    // });
    // console.log(tanggal);

    // definisi optinfilter untuk query database
    let optionFilter = {};

    // ambil data dari request GET
    const dari = req.query.dari;
    const sampai = req.query.sampai;
    const status = req.query.status;
    const semua = req.query.semua;
    const search = req.query.search;

    // Pengecekan filter berdasarkan tanggal
    if (dari && sampai) {
        // Kalo tanggal nya sama
        if (dari === sampai) {
            // ambil hari esok
            let besoknya = dateTomorrow(dari);
            // tambah filter
            optionFilter = { time: { $gte: new Date(dari), $lte: new Date(besoknya) } };
        }
        // Kalo tanggal nya beda
        else {
            besoknya = dateTomorrow(sampai);
            optionFilter = { time: { $gte: new Date(dari), $lte: new Date(besoknya) } };
        }
    }
    // Kalo salah satu diantara tanggal yang terisi cuma 1
    else if (dari || sampai) {
        const ininya = dari || sampai;
        optionFilter = { upload: ininya };
    }
    // Kalo gadaada yang terisi tanggal nya ambil hari ini
    else {
        const hariini = new Date().toISOString().split('T')[0];
        optionFilter = { upload: hariini };
    }

    // Cek apakah ada status yang ditambahkan
    if (status != undefined) {
        optionFilter = { status };
        // optionFilter.status = status;
    }

    // Cek apakah request memenita semua
    if (search != undefined) {
        optionFilter = {
            $or: [
                { lokasi: { $regex: search.toUpperCase() } },
                { tipeaset: { $regex: search.toUpperCase() } },
                { sn: { $regex: search.toUpperCase() } }
            ]
        }
    }

    // Cek apakah request memenita semua
    if (semua != undefined) {
        optionFilter = null;
    }

    console.log(optionFilter)

    // ambil data dari Model
    const data = await AssetKerja.find(optionFilter);



    // tampikan view
    res.render('cekstatus', { layout: 'layouts/main-layout', title: 'Home', adagak: req.query, data, tanggal, msg: req.flash('edit') });

})

app.listen(port, () => {
    console.log(`Example app listening on port ${port}!`);
});

//Run app, then load http://localhost:3000 in a browser to see the output.
module.exports = app