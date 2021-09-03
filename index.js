const express = require('express');
const expressLayouts = require('express-ejs-layouts');
const { body, validationResult, check } = require('express-validator');
const methodOverride = require('method-override');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const flash = require('connect-flash');

const app = express();
const port = process.env.PORT || 3000;

// Setup connection DB
require('./utils/db');
const AssetKerja = require('./models/assetkerja');

// SetUp EJS
app.set('view engine', 'ejs');
app.use(expressLayouts);
app.use(express.static('public'));
app.use(express.urlencoded({ extended: true }));

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

app.get('/ini', (req, res) => {
    res.send('Hello Mas Teguh');
});

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
            req.flash('msg', 'Data Contact berhasil ditambahkan');
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

app.get('/', async(req, res) => {

    // bikin fungsi untuk besoknya
    const dateTomorrow = (date) => {
        const sekarang = new Date(date)
        sekarang.setDate(sekarang.getDate() + 1);
        return sekarang.toISOString().split('T')[0];
    };

    // Ambil tanggal untuk pencarian
    const tanggal = await AssetKerja.distinct('upload');
    tanggal.reverse();

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
        optionFilter = { $or: [{ lokasi: { $regex: search.toUpperCase() } }, { tipeaset: { $regex: search.toUpperCase() } }] }
    }

    // Cek apakah request memenita semua
    if (semua != undefined) {
        optionFilter = null;
    }

    // console.log(optionFilter)

    // ambil data dari Model
    const data = await AssetKerja.find(optionFilter);



    // tampikan view
    res.render('cekstatus', { layout: 'layouts/main-layout', title: 'Home', adagak: req.query, data, tanggal, msg: req.flash('edit') });

})

app.listen(port, () => {
    console.log(`Example app listening on port ${port}!`);
});

//Run app, then load http://localhost:3000 in a browser to see the output.