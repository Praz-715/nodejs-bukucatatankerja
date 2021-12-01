const mongoose = require("mongoose");

const AssetKerja = mongoose.model("AssetKerja", {
    lokasi:     { type: String, required: true },
    tipeaset:   { type: String, required: true },
    sn:         { type: String, required: true },
    status:     { type: String, required: true },
    keterangan: { type: [String], required: true },
    upload:     { type: String, require: true },
    time:       { type: Date, default: Date.now },
});

module.exports = AssetKerja;