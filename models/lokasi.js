const mongoose = require("mongoose");

const Lokasi = mongoose.model("Lokasi", {
    kode:       { type: String, required: true },
    lokasi:     { type: String, required: true },
    is_leasing: { type: Boolean, required: true },
});

module.exports = Lokasi;