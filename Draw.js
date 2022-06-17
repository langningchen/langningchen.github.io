ThisDrawObject = null;
ThisClassHomeworkUploadID = 0;
function ClearImage() {
    var ObjectWidth = Math.max(ThisDrawObject.height, ThisDrawObject.width);
    var ObjectHeight = Math.min(ThisDrawObject.height, ThisDrawObject.width);
    var CanvasDiv = document.getElementById("CanvasDiv");
    var CanvasDivContext = CanvasDiv.getContext("2d");
    CanvasDiv.height = window.innerHeight - 50;
    CanvasDiv.width = CanvasDiv.height / ObjectHeight * ObjectWidth;
    CanvasDiv.getContext("2d").drawImage(ThisDrawObject, 0, 0, CanvasDiv.width, CanvasDiv.height);
    CanvasDivContext.strokeStyle = "red";
    CanvasDivContext.lineWidth = 3;
}
function UploadImage() {
    var HttpRequest;
    if (window.XMLHttpRequest) {
        HttpRequest = new XMLHttpRequest();
    } else {
        HttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }
    HttpRequest.onreadystatechange = function() {
        if (HttpRequest.readyState == 4) {
            if (HttpRequest.status == 200 && HttpRequest.responseText == "OK") {
                DrawDiv.parentNode.removeChild(DrawDiv);
            } else {
                alert("提交失败：" + HttpRequest.responseText);
            }
        }
    }
    var canvas = document.getElementById("CanvasDiv");
    var img = canvas.toDataURL();
    HttpRequest.open("POST", "UploadCorrectPicture.php", true);
    HttpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    HttpRequest.send("ClassHomeworkUploadID=" + ThisClassHomeworkUploadID + "&" + "Image=" + img);
}
function Draw(object, ID) {
    ThisDrawObject = object;
    ThisClassHomeworkUploadID = ID;
    var DrawDiv = document.createElement("div");
    DrawDiv.style.position = "fixed";
    DrawDiv.style.left = "0%";
    DrawDiv.style.top = "0%";
    DrawDiv.style.width = "100%";
    DrawDiv.style.height = "100%";
    DrawDiv.id = "DrawDiv";
    DrawDiv.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    var CloseDiv = document.createElement("div");
    CloseDiv.style.position = "fixed";
    CloseDiv.style.right = "10px";
    CloseDiv.style.top = "10px";
    CloseDiv.style.margin = "5px";
    CloseDiv.innerHTML = "";
    CloseDiv.innerHTML += "<input type=\"button\" onclick=\"DrawDiv.parentNode.removeChild(DrawDiv);\" class=\"DangerousButton\" value=\"取消批改\" />";
    CloseDiv.innerHTML += "<br />";
    CloseDiv.innerHTML += "<input type=\"button\" onclick=\"UploadImage()\" class=\"MainButton\" value=\"提交批改\" />";
    DrawDiv.appendChild(CloseDiv);
    var ChangeDiv = document.createElement("div");
    ChangeDiv.style.position = "fixed";
    ChangeDiv.style.bottom = "-10px";
    ChangeDiv.style.height = "50px";
    ChangeDiv.innerHTML = "";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='red'\" style=\"color: red; background-color: red; height: 20px; width: 20px;\" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='green'\" style=\"color: green; background-color: green; height: 20px; width: 20px;\" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='blue'\" style=\"color: blue; background-color: blue; height: 20px; width: 20px;\" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='black'\" style=\"color: black; background-color: black; height: 20px; width: 20px;\" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='white'\" style=\"color: white; background-color: white; height: 20px; width: 20px;\" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"ClearImage()\" style=\"color: black; text-align: center; position: relative; top: -2px; font-size: 10px; background-color: white; height: 20px; width: 40px;\" value=\"清空\" />";
    DrawDiv.appendChild(ChangeDiv);
    var CanvasDiv = document.createElement("canvas");
    CanvasDiv.id = "CanvasDiv";
    CanvasDiv.innerHTML = "您的浏览器不支持画布";
    DrawDiv.appendChild(CanvasDiv);
    document.body.appendChild(DrawDiv);
    ClearImage();
    var CanvasDiv = document.getElementById("CanvasDiv");
    var CanvasDivContext = CanvasDiv.getContext("2d");
    CanvasDiv.onmousedown = function(e) {
        var ev = window.event || e;
        var jiu_left = ev.layerX || ev.offsetX;
        var jiu_top = ev.layerY || ev.offsetY;
        CanvasDivContext.beginPath();
        CanvasDivContext.moveTo(jiu_left, jiu_top);
        CanvasDiv.onmousemove = function(e) {
            var ev = window.event || e;
            var now_left = ev.layerX || ev.offsetX;
            var now_top = ev.layerY || ev.offsetY;
            CanvasDivContext.lineTo(now_left, now_top);
            CanvasDivContext.stroke();
        }
    }
    CanvasDiv.onmouseup = function() {
        CanvasDiv.onmousemove = null;
    }
}