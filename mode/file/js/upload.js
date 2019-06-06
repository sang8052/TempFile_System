// 文件前端直传 上传签名
var File_Sign="";
var Sign_Data="";
function GetCodeStatus()
{
    var code=document.getElementById("file_code").value;
    if(code=="") console.log(GetTimeNow()+"尝试获得新文件标识");
    $.ajax({
        url:"file.php?fun=code",
        async:false,
        data:"code="+code,
        timeout:2000,
        type:"POST",
        dataType:"json",
        success:function (rdata) {
            console.log(GetTimeNow()+"服务器返回文件标识数据");
            if(rdata['status']=="free")
            {
                document.getElementById("code_status").style="color:green";
                document.getElementById("code_status").setAttribute("status","free");
                document.getElementById("code_status").innerText="可以使用";
                document.getElementById("file_code").value=rdata['code'];
            }
            else
            {
                document.getElementById("code_status").style="color:red";
                document.getElementById("code_status").setAttribute("status","used");
                document.getElementById("code_status").innerText="地址占用";
                document.getElementById("file_code").value=rdata['code'];
            }
        },
        error:function (error) {
            console.log(GetTimeNow()+"服务器返回文件标识数据出错!");
            alert("[Error]:Request File Code Failed!")
        }
    })
}



function ClickInput()
{

    if(document.getElementById("code_status").getAttribute("status")!="free"||document.getElementById("file_code").value=="")
        alert("[Error]:Check Your File Code First!");
    else
    {
        console.log(GetTimeNow()+"触发上传文件点击事件");
        document.getElementById("file_upload").click();
    }
}


function file_submit(callback)
// 执行文件上传
{
    if(!callback)
    {
        console.log(GetTimeNow()+"准备执行文件上传，获取上传签名");
        Get_UploadSign();
    }
    else
    {
        console.log(GetTimeNow()+"构造前端数据传输表单")
        let  request=new FormData();
        var UploadSign=File_Sign;
        request.append("OSSAccessKeyId", UploadSign['OSSAccessKeyID']);
        request.append("policy",UploadSign['policy']);
        request.append("Signature",UploadSign['Signature']);
        request.append("callback",UploadSign['callback']);
        request.append("key",UploadSign['object']); //文件名字，可设置路径
        request.append("success_action_status", '200'); // 让服务端返回200,不然，默认会返回204
        request.append('file', document.getElementById("file_upload").files[0]); //需要上传的文件 file
        console.log(GetTimeNow()+"表单构造完毕，准备上传数据");
        $.ajax({
            url: UploadSign['host'],
            data: request,
            cache: false,
            async: true,
            contentType: false,
            processData:false,
            xhr: function xhr() {

                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (e) {
                        percentage = parseInt(e.loaded / e.total * 100);
                        console.log(GetTimeNow()+"文件上传中...进度:"+percentage+"%");
                        document.getElementById("upload_per").style="width:"+percentage+"%";

                    }, false);
                }
                return xhr;
            },
            dataType: 'json',
            type: 'post',
            success: function (rdata) {
                if(rdata['status']=="success")
                {
                    console.log(GetTimeNow() + "文件上传成功");
                    document.getElementById("file_down_show").innerHTML="<a target=\"_blank\" href=\""+rdata['url']+"\">"+rdata['url']+"</a>";
                    document.getElementById("file_time_show").innerHTML=rdata['DelTime'];

                    alert("[Notice]文件上传成功");
                }
                else {
                    alert("[Error]:文件上传失败，特征代码已经被占用!");
                    window.location.href = "file.php";
                }
            },
            error:function (e) {
                alert("[Error]:文件上传失败，OSS 系统数据通信异常");
            }
        })
    }

}

function Get_UploadSign()
{
    if(!Sign_Data||Sign_Data=="")
        alert("[Error]:Please Choose File Need Upload!");
    else
    {
        $.ajax({
            url:"file.php?fun=sign",
            type:"POST",
            timeout:2500,
            dataType:"json",
            data:Sign_Data,
            success:function(rdata){ if(rdata['status']=="success"){  console.log(GetTimeNow()+"获得文件上传签名成功，有效期30S"); File_Sign=rdata['data'];file_submit(true)}else alert("[Error]:Sign File Failed!");},
            error:function (e) {alert("[Error]:Sign File Failed!");},
        })
    }
}

function  Upload_Sign(file)
{

    console.log(GetTimeNow()+"更新文本框中的内容");
    document.getElementById("file_name").value=file.value;
    var File=file.files[0];
    Sign_Data="code="+document.getElementById("file_code").value+"&num="+document.getElementById("download_max_num").value;
    Sign_Data=Sign_Data+"&filename="+File.name+"&filetype="+GetFileType(File.name)+"&filesize="+File.size;
    console.log(GetTimeNow()+"编码签名数据");
}

function  GetFileType(filename) {
    var index = filename.lastIndexOf(".");
    var suffix = filename.substr(index+1);
    return suffix;

}

function GetTimeNow()
{
    var NowDate = new Date();
    var NowHour= TimeZeroCheck(NowDate.getHours());
    var NowMin= TimeZeroCheck(NowDate.getMinutes());
    var NowSec= TimeZeroCheck(NowDate.getSeconds());
    var NowMSec= TimeZeroCheck(NowDate.getMilliseconds());
    return NowHour+":"+NowMin+":"+NowSec+":"+NowMSec+" ";
}

function TimeZeroCheck(num)
{
    if(num < 10) return "0"+num;
    else return num;
}
GetCodeStatus();