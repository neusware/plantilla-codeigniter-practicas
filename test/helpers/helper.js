
module.exports = {
    dateToUnix: function(dateString) {
        return parseInt((new Date(dateString)).getTime() / 1000)
    }
}