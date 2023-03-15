(function ( $ ) {
    /**
     *
     *
     * @param url
     * @param cb
     * @returns {jQuery}
     */
    $.fetch = function(url, cb) {
        fetch(url).then(res=>{
            if (res.status === 200){
                // SUCCESS
                res.json().then(data=>{
                    cb(data)
                }).catch(err => console.log(err))
            }
            else{
                // ERROR
            }
        })
        return this;
    };

    /**
     *
     *
     * @param url
     * @param cb
     * @returns {jQuery}
     */
    $.fn.getPayload = function() {
        const fields = this.serializeArray()
        let payload = {};
        for (let i in fields) {
            payload[fields[i].name] = fields[i].value
        }
        return payload;
    };

}( jQuery ));
