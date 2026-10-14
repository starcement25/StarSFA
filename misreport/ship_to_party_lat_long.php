<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="") header("location:index.php");
disphtml("main();");
ob_end_flush();

function main()
{
?>
<style>
    .shiptoparty-page-wrap {
        width: 70%;
        max-width: 900px;
        margin: 28px auto;
        color: #243447;
        font-family: Arial, sans-serif;
    }

    .shiptoparty-message-panel {
        margin-bottom: 18px;
        text-align: center;
        color: #c62828;
        font-weight: bold;
        min-height: 24px;
    }

    .shiptoparty-upload-card {
        background: #ffffff;
        border: 1px solid #cfd8e3;
        box-shadow: 0 10px 28px rgba(36, 52, 71, 0.08);
    }

    .shiptoparty-card-head {
        padding: 14px 18px;
        background: linear-gradient(90deg, #A92A61 , #A92A61);
        color: #ffffff;
        font-size: 18px;
        font-weight: bold;
    }

    .shiptoparty-card-body {
        padding: 24px 28px 28px;
    }

    .shiptoparty-form-table {
        width: 100%;
        border-collapse: collapse;
    }

    .shiptoparty-form-table td {
        padding: 10px 6px;
        vertical-align: middle;
    }

    .shiptoparty-label-cell {
        width: 180px;
        text-align: right;
        font-weight: bold;
        color: #334e68;
    }

    .shiptoparty-colon-cell {
        width: 16px;
        text-align: center;
        color: #334e68;
    }

    .shiptoparty-file-input {
        width: 100%;
        max-width: 360px;
        padding: 10px;
        border: 1px solid #c5d0db;
        background: #f8fafc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .shiptoparty-note {
        display: block;
        margin-top: 8px;
        color: #c62828;
        font-weight: bold;
        font-size: 13px;
    }

    .shiptoparty-actions {
        padding-top: 8px;
    }

    .shiptoparty-btn {
        min-width: 110px;
        padding: 10px 18px;
        margin-right: 10px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .shiptoparty-btn-primary {
        background: #A92A61 ;
        color: #ffffff;
    }

    .shiptoparty-btn-secondary {
        background: #e6eef7;
        color: #1f4e79;
        border: 1px solid #b8cbe0;
    }

    .shiptoparty-status-message {
        margin-top: 18px;
        padding: 12px 14px;
        border: 1px solid #d7e1eb;
        background: #f8fafc;
        color: #1f2933;
        min-height: 20px;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .shiptoparty-page-wrap {
            width: calc(100% - 24px);
            margin: 20px auto;
        }

        .shiptoparty-card-body {
            padding: 18px;
        }

        .shiptoparty-form-table,
        .shiptoparty-form-table tbody,
        .shiptoparty-form-table tr,
        .shiptoparty-form-table td {
            display: block;
            width: 100%;
        }

        .shiptoparty-label-cell {
            text-align: left;
            padding-bottom: 4px;
        }

        .shiptoparty-colon-cell {
            display: none;
        }

        .shiptoparty-btn {
            width: 100%;
            margin: 0 0 10px;
        }
    }
</style>

<div class="shiptoparty-page-wrap">
    <div class="shiptoparty-message-panel" id="topMsg"></div>

    <div class="shiptoparty-upload-card">
        <div class="shiptoparty-card-head">Ship To Party Lat Long Upload</div>

        <div class="shiptoparty-card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <table class="shiptoparty-form-table">
                    <tr>
                        <td class="shiptoparty-label-cell">CSV File*</td>
                        <td class="shiptoparty-colon-cell">:</td>
                        <td>
                            <input type="file" name="csv_file" accept=".csv" required class="shiptoparty-file-input">
                            <span class="shiptoparty-note">[Extension will be .csv]</span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="shiptoparty-colon-cell">&nbsp;</td>
                        <td class="shiptoparty-actions">
                            <button type="submit" class="shiptoparty-btn shiptoparty-btn-primary">Upload</button>
                            <button type="button" class="shiptoparty-btn shiptoparty-btn-secondary" onclick="downloadCSV()">Download Sample CSV</button>
                        </td>
                    </tr>
                </table>
            </form>

            <div class="shiptoparty-status-message" id="msg"></div>
        </div>
    </div>
</div>

<script>
document.getElementById("uploadForm").onsubmit = function(e){
    e.preventDefault();

    var formData = new FormData(this);

    document.getElementById("msg").innerHTML = "Uploading, please wait...";
    document.getElementById("topMsg").innerHTML = "";

    fetch("ship_to_party_lat_long_upload.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("msg").innerHTML = data.message;
        document.getElementById("topMsg").innerHTML = data.message;
    })
    .catch(err => {
        document.getElementById("msg").innerHTML = "Upload failed : " + err;
        document.getElementById("topMsg").innerHTML = "Upload failed";
    });
};

function downloadCSV(){
    window.location.href = "ship_to_party_lat_long_sample.php";
}
</script>
<?php
}
?>
