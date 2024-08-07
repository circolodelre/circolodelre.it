(function ( $ ) {
    /**
     *
     * @type {{}}
     */
    $.app = {};

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
     * @param country
     * @returns {string}
     */
    $.countryFlag = function (country) {
        return String.fromCodePoint(...[...country.toUpperCase()].map(c => c.charCodeAt() + 0x1F1A5));
    }

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

    /**
     *
     * @param strData
     * @param strDelimiter
     *
     * @returns {*[][]}
     */
    $.parseCsv = function (strData, strDelimiter) {
        // Check to see if the delimiter is defined. If not,
        // then default to comma.
        strDelimiter = (strDelimiter || ",");

        // Create a regular expression to parse the CSV values.
        var objPattern = new RegExp(
            (
                // Delimiters.
                "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

                // Quoted fields.
                "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

                // Standard fields.
                "([^\"\\" + strDelimiter + "\\r\\n]*))"
            ),
            "gi"
        );


        // Create an array to hold our data. Give the array
        // a default empty first row.
        var arrData = [[]];

        // Create an array to hold our individual pattern
        // matching groups.
        var arrMatches = null;


        // Keep looping over the regular expression matches
        // until we can no longer find a match.
        while (arrMatches = objPattern.exec( strData )){

            // Get the delimiter that was found.
            var strMatchedDelimiter = arrMatches[ 1 ];

            // Check to see if the given delimiter has a length
            // (is not the start of string) and if it matches
            // field delimiter. If id does not, then we know
            // that this delimiter is a row delimiter.
            if (
                strMatchedDelimiter.length &&
                (strMatchedDelimiter != strDelimiter)
            ){

                // Since we have reached a new row of data,
                // add an empty row to our data array.
                arrData.push( [] );

            }


            // Now that we have our delimiter out of the way,
            // let's check to see which kind of value we
            // captured (quoted or unquoted).
            if (arrMatches[ 2 ]){

                // We found a quoted value. When we capture
                // this value, unescape any double quotes.
                var strMatchedValue = arrMatches[ 2 ].replace(
                    new RegExp( "\"\"", "g" ),
                    "\""
                );

            } else {

                // We found a non-quoted value.
                var strMatchedValue = arrMatches[ 3 ];

            }


            // Now that we have our value string, let's add
            // it to the data array.
            arrData[ arrData.length - 1 ].push( strMatchedValue );
        }

        // Return the parsed data.
        return( arrData );
    }

    /**
     *
     * @param component
     */
    $.app.loadEventJoined = function (component) {
        let countJoined = 0;
        const eventName = component.getAttribute("event-name")
        const csvUrl = component.getAttribute("csv-url")
        fetch(csvUrl, { redirect: "follow" })
            .then(response => response.text())
            .then(text => {
                $.parseCsv(text).forEach((line) => {
                    const [timestamp, name, category, elo, currentEventName] = line
                    if (currentEventName === eventName) {
                        if (!countJoined) {
                            component.innerHTML = ""
                        }
                        const row = document.createElement("tr")
                        const index = component.childElementCount + 1;
                        row.innerHTML = `<td class="has-text-centered">${index}</td><td>${name}</td><td class="has-text-centered">${category}</td><td class="has-text-centered">${elo}</td>`;
                        component.appendChild(row)
                        countJoined++;
                    }
                })
                if (!countJoined) {
                    component.innerHTML = `<tr><td class="has-text-centered has-text-grey" colspan="4">Nessun iscritto</td></tr>`;
                }
            });
    };

    /**
     * Loop through all components and call the function
     */
    $(document).ready(() => {
        document.querySelectorAll("[app-component]").forEach((component) => {
            $.app[component.getAttribute("app-component")].call(null, component)
        });
    });

}( jQuery ));
