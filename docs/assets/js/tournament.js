
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
    });
    $('.select-portal-button button', element).click((event) => {
        const button = $(event.currentTarget);
        const portal = button.parent().data('portal');
        $('input[name=portal]', element).val(portal);
        $('.select-portal-button', element).removeClass('selected')
        button.parent().addClass('selected')
    })
})


function SubForm (){
    let data = $("#myForm").getPayload();



    console.log("NICK:", data.nickname)

    data.datetime = new Date().toISOString().slice(0, 19).replace("T", " ")

    GetProfile(data, data => {
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
    })
}

function GetProfile(data, cb) {
    if (data.portal == 'chess-com') {
        $.fetch("https://api.chess.com/pub/player/"+data.nickname, resp =>{
            console.log("Chess.com:", resp)
            data.avatar = resp.avatar
            data.rating = 1000
            cb(data)
        })
    } else {
        $.fetch("https://lichess.org/api/user/"+data.nickname, resp => {
            console.log("lichess.org:", resp)
            data.rating = 2000
            cb(data)
        })
    }
}