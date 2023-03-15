(function ( $ ) {

    $.fn.fetch = function(url, cb) {
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

}( jQuery ));

