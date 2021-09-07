const mongoose = require("mongoose");

const Report = mongoose.model("Report", {
    name: { type: String, required: true },
    filePath: { type: String, required: true },
});

module.exports = Report;