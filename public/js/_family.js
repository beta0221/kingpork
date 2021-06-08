const API_key = "a6ff1b7c1976641e287b05794cd589ff36d9a6e896d8f4402199a8f9f0998240d9bc68594ba4fed8ef678e49fd09cdec9fbd11fb5ba0f2b7aa1b521ddb2b78b1";
const Map_url = "https://family.orders.ilohas.info/map/FamilyMap.php";
const Origin_url = "https://family.orders.ilohas.info";

//新開視窗並置中開啟
function openwindow(url,name,iWidth,iHeight)
{
	var url;
	var name;
	var iWidth;
	var iHeight;
	var iTop = (window.screen.availHeight-30-iHeight)/2;
	var iLeft = (window.screen.availWidth-10-iWidth)/2;
	window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',status=no,location=no,status=no,menubar=no,toolbar=no,resizable=no,scrollbars=yes');
}

//啟動視窗
function call_windows()
{
    let url = Map_url + "?k=" + API_key;
	openwindow(url,'',500,450)

    // $("input[name='store_number']").val("017267");
    // $("input[name='store_name']").val("仁愛高山青店　Ｂ");
    // $("input[name='store_address']").val("南投縣仁愛鄉大同村信義巷34之6號");
}

//處理回傳資訊()
var myMsg = function(e) {
	if (e.origin != Origin_url) {
		return; //不明來源,不處理
	}
	//解析javascript回傳的資料
    console.log(e.data);
    let data = JSON.parse(e.data);
    $("input[name='store_number']").val(data.StoreNumber);
    $("input[name='store_name']").val(data.StoreName);
    $("input[name='store_address']").val(data.StoreAdd);

};

//監聽message事件
window.addEventListener("message", myMsg, false);
