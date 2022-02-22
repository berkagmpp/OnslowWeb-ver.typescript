"use strict";

//listrooms.php 
(function () {
    const getRooms = () => {
        return new Promise<string>((resolve, reject): void => {
            const xhttp: XMLHttpRequest = new XMLHttpRequest();

            xhttp.onreadystatechange = (): void => {
                if (xhttp.readyState !== XMLHttpRequest.DONE) {
                    return;
                }
                if (xhttp.status >= 200 && xhttp.status < 400) {
                    resolve(JSON.parse(xhttp.responseText));
                } else {
                    reject({
                        // statusText: xhttp.statusText,
                        error: xhttp.status
                        // new Error(xhttp.status)
                    });
                }
            }

            xhttp.open("GET", "/onslow-ts/api/rooms.php?sq=", true);
            xhttp.send();
        });
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const display = (data: any):any => {
        const tbl = document.getElementById("tblrooms") as HTMLTableElement;
        const rowCount = tbl.rows.length;
        for (let i = 1; i < rowCount; i++) {
            //delete from the top - row 0 is the table header we keep
            tbl.deleteRow(1);
        }

        //populate the table
        //data.length is the size of our array
        for (let i = 0; i < data.length; i++) {

            const id: number = data[i]['roomID'];
            const roomName: string = data[i]['roomname'];
            const roomType: string = data[i]['roomtype'];

            //concatenate our actions urls into a single string
            let urls: string = '<a href="viewroom.php?id=' + id + '">[view]</a>';
            urls += '<a href="editroom.php?id=' + id + '">[edit]</a>';
            urls += '<a href="deleteroom.php?id=' + id + '">[delete]</a>';

            //create a table row with three cells  
            const tr = tbl.insertRow(-1);
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            let tabCell: any = tr.insertCell(-1);
            tabCell.innerHTML = id;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = roomName; //name
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = roomType; //type
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = urls; //action URLS            
        }
    }

    getRooms()
        .then(data => {
            display(data);
        }).catch(console.error);
})();
