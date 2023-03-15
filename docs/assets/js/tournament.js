
const SHEET_API = "https://api.apispreadsheets.com/data/NQrCKaNLm8SVlYSZ/?query="

$('.tournament-registration').each((index, element) => {
    const tournament = $(element).data('tournament')
    console.log('T:', tournament);
    const url = SHEET_API + "select * from NQrCKaNLm8SVlYSZ where tournament='"+tournament+"'"
    $.fetch(url, (data) => {
        console.log("DATA:", data);
        let number = 1;
        for (let i in data.data) {
            let user = data.data[i];
            $('tbody', element).append(`<tr><td>${number}</td><td>${user.nickname}</td></tr>`)
            number++;
        }
    })
})


function SubForm (){
    let data = $("#myForm").getPayload();



    console.log("NICK:", data.nickname)

    $.fetch("https://api.chess.com/pub/player/"+data.nickname, data =>{
        console.log("Chess.com:", data)
    })

    $.fetch("https://lichess.org/api/user/"+data.nickname, data => {
        console.log("lichess.org:", data)
    })

    $.ajax({
        url:"https://api.apispreadsheets.com/data/NQrCKaNLm8SVlYSZ/",
        type:"post",
        data: data,
        success: function(){
            //alert("Form Data Submitted :)")
        },
        error: function(){
            //alert("There was an error :(")
        }
    });
}