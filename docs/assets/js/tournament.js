
const SHEET_API = "https://api.apispreadsheets.com/data/NQrCKaNLm8SVlYSZ/?query="

$('.tournament-registration').each((index, element) => {
    const tournament = $(element).data('tournament')
    console.log('T:', tournament);

    LoadUsers(tournament, element);
    $('.select-portal-button button', element).click((event) => {
        const button = $(event.currentTarget);
        const portal = button.parent().data('portal');
        $('input[name=portal]', element).val(portal);
        $('.select-portal-button', element).removeClass('selected')
        button.parent().addClass('selected')
    })
})

function LoadUsers(tournament, element) {
    const url = SHEET_API + "select * from NQrCKaNLm8SVlYSZ where tournament='"+tournament+"'"
    $.fetch(url, (data) => {
        let users = data.data.sort((a, b) => {
            return (parseInt(a.rating) < parseInt(b.rating)) ? 1
                : (parseInt(a.rating) > parseInt(b.rating) ? -1 : 0);
        });
        console.log("DATA:", data);
        let number = 1;
        for (let i in users) {
            let user = users[i];
            user.countryFlag = $.countryFlag(user.country)
            $('tbody', element).append(`
                <tr>
                    <th class="has-text-centered">${number}</th>
                    <td>
                        <figure class="image is-32x32">
                            <img class="is-rounded" src="${user.avatar}"/>
                        </figure>
                    </td>
                    <td><a href="${user.url}" target="_blank">${user.nickname}</a></td>
                    <td>${user.countryFlag}</td>
                    <td class="has-text-centered">${user.rating}</td>           
                    <td class="has-text-centered">${user.portal}</td>           
                </tr>
            `)
            number++;
        }
    });
}

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
            data.rating = 0
            data.country = resp.country.slice(-2)
            data.url = resp.url
            $.fetch("https://api.chess.com/pub/player/"+data.nickname+"/stats", resp =>{
                console.log("Chess.com STATS:", resp)
                const gameType = ['chess_blitz'];
                for (let i in gameType) {
                    const gameRating = parseInt(resp[gameType[i]].last.rating);
                    if (gameRating > data.rating) {
                        data.rating = gameRating
                    }
                }
                cb(data)
            })
        })
    } else {
        $.fetch("https://lichess.org/api/user/"+data.nickname, resp => {
            console.log("lichess.org:", resp)
            data.rating = 0
            const gameType = ['blitz', 'bullet', 'classical', 'correspondence', 'rapid'];
            for (let i in gameType) {
                const gameCount = parseInt(resp.perfs[gameType[i]].games);
                const gameRating = parseInt(resp.perfs[gameType[i]].rating);
                if (gameCount > 0 && gameRating > data.rating) {
                    data.rating = gameRating
                }
            }
            cb(data)
        })
    }
}