'use strict';

//listrooms.php 
(function () { 
    let rooms = [];

    const getRooms = function() {
      return new Promise ((resolve, reject) => {
        const xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
          if (xhttp.readyState !== XMLHttpRequest.DONE) {
            return;
          }
          if (xhttp.status >= 200 && xhttp.status < 400) {
            resolve(JSON.parse(xhttp.responseText));
          } else {
            reject(new Error(xhttp.status));
          }
        }

        xhttp.open("GET", "/onslow/api/rooms.php?sq=", true);
        xhttp.send();
      });
    }

    const display = function(data) {
      let tbl = document.getElementById("tblrooms");
      let rowCount = tbl.rows.length;
      for (let i = 1; i < rowCount; i++) {
        //delete from the top - row 0 is the table header we keep
        tbl.deleteRow(1);
      }

      //populate the table
      //mbrs.length is the size of our array
      for (let i = 0; i < data.length; i++) {
        let id = data[i]['roomID'];
        let roomName = data[i]['roomname'];
        let roomType = data[i]['roomtype'];

        //concatenate our actions urls into a single string
        let urls = '<a href="viewroom.php?id=' + id + '">[view]</a>';
        urls += '<a href="editroom.php?id=' + id + '">[edit]</a>';
        urls += '<a href="deleteroom.php?id=' + id + '">[delete]</a>';

        //create a table row with three cells  
        let tr = tbl.insertRow(-1);
        let tabCell = tr.insertCell(-1);
        tabCell.innerHTML = id; //id
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
        rooms = data;
        display(rooms);
      }).catch(console.error);
})();
