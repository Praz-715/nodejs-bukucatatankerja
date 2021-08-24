const mongoose = require('mongoose');

mongoose.connect('mongodb+srv://teguh:ganteng@cluster0.r0ah9.mongodb.net/myFirstDatabase?retryWrites=true&w=majority', {
    useNewUrlParser: true,
    useUnifiedTopology: true,
    useCreateIndex: true
});