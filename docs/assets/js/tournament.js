

$('.tournament-registration').each((tournament) => {
    $(tournament).data('tournament')

    fetch("https://api.apispreadsheets.com/data/NQrCKaNLm8SVlYSZ/?query=select * from NQrCKaNLm8SVlYSZ where tournament='ciao'").then(res=>{
        if (res.status === 200){
            // SUCCESS
            res.json().then(data=>{
                const yourData = data.data
                console.log(yourData)
            }).catch(err => console.log(err))
        }
        else{
            // ERROR
        }
    })

})


function SubForm (){
    let data = {}
    let fields = $("#myForm").serializeArray()
    for (let i in fields) {
        data[fields[i].name] = fields[i].value
    }
    console.log(data.nickname)

    fetch("https://api.chess.com/pub/player/"+data.nickname).then(res=>{
        if (res.status === 200){
            // SUCCESS
            res.json().then(data=>{
                const yourData = data
                console.log("Chess.com:", yourData)
            }).catch(err => console.log(err))
        }
        else{
            // ERROR
        }
    })


    fetch("https://lichess.org/api/user/"+data.nickname).then(res=>{
        if (res.status === 200){
            // SUCCESS
            res.json().then(data=>{
                const yourData = data
                console.log("lichess.org:", yourData)
            }).catch(err => console.log(err))
        }
        else{
            // ERROR
        }
    })

    $.ajax({
        url:"https://api.apispreadsheets.com/data/NQrCKaNLm8SVlYSZ/",
        type:"post",
        data: $("#myForm").serializeArray(),
        success: function(){
            //alert("Form Data Submitted :)")
        },
        error: function(){
            //alert("There was an error :(")
        }
    });
}