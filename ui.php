<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    #customers {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }

    #customers td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
    }

    #customers tr:nth-child(even){background-color: #f2f2f2;}

    #customers tr:hover {background-color: #ddd;}

    #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #04AA6D;
    color: white;
    }
    .button {
    transition-duration: 0.4s;
    }

    .button:hover {
    background-color: #04AA6D; /* Green */
    color: white;
    }
    .button {
    background-color: #04AA6D; /* Green */
    border: none;
    color: white;
    padding: 16px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    transition-duration: 0.4s;
    cursor: pointer;
    }
    input[type=text], select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    }


    </style>
</head>
<body>
    <br>
    <label for="city">City Name</label>
    <input type="text" id="city_name">
    <button class="button" id="search">search</button>
    <h3 style="display:none" id="loading">Scraping...</h3>
    <br>
    <br>
    <table id="customers">
      <tr>
        <th>Name of the business</th>
        <th>Website</th>
        <th>Phone number</th>
        <th>Address</th>
      </tr>
    </table>
</body>
<script>
    const searchButton = document.getElementById("search");
    const cityName = document.getElementById("city_name");
    searchButton.onclick = function(){
        let name = cityName.value;
        document.getElementById("customers").innerHTML = '<tr><th>Name of the business</th><th>Website</th><th>Phone number</th><th>Address</th></tr>';
        document.getElementById("loading").style.display = "block";
        var xmlhttp;
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function(){
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
              document.getElementById("loading").style.display = "none";
              let rlt = JSON.parse(xmlhttp.responseText);
              console.log(rlt);
              let comp = "";
              for(let i = 0;i<rlt.length;i++){
                if(comp == rlt[i][3]) continue;
                comp = rlt[i][3];
                let row = document.createElement("tr");
                let col0 = document.createElement("td");
                col0.innerText = rlt[i][0];
                let col1 = document.createElement("td");
                col1.innerText = rlt[i][1];
                let col2 = document.createElement("td");
                col2.innerText = rlt[i][2];
                let col3 = document.createElement("td");
                col3.innerText = rlt[i][3];
                row.append(col0, col1, col2, col3);
                document.getElementById("customers").append(row);
              }
            }
        }
        $shURL = "index.php?q="+name;
        xmlhttp.open("POST", $shURL, true);
        xmlhttp.send();
    }
</script>
</html>