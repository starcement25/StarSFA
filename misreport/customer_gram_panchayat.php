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
    .gpupload-page-wrap {
        width: 70%;
        max-width: 900px;
        margin: 28px auto;
        color: #243447;
        font-family: Arial, sans-serif;
    }

    .gpupload-message-panel {
        margin-bottom: 18px;
        text-align: center;
        color: #c62828;
        font-weight: bold;
        min-height: 24px;
    }

    .gpupload-upload-card {
        background: #ffffff;
        border: 1px solid #cfd8e3;
        box-shadow: 0 10px 28px rgba(36, 52, 71, 0.08);
    }

    .gpupload-card-head {
        padding: 14px 18px;
        background: #A92A61;
        color: #ffffff;
        font-size: 18px;
        font-weight: bold;
    }

    .gpupload-card-body {
        padding: 24px 28px 28px;
    }

    .gpupload-form-table {
        width: 100%;
        border-collapse: collapse;
    }

    .gpupload-form-table td {
        padding: 10px 6px;
        vertical-align: middle;
    }

    .gpupload-label-cell {
        width: 180px;
        text-align: right;
        font-weight: bold;
        color: #334e68;
    }

    .gpupload-colon-cell {
        width: 16px;
        text-align: center;
        color: #334e68;
    }

    .gpupload-file-input {
        width: 100%;
        max-width: 360px;
        padding: 10px;
        border: 1px solid #c5d0db;
        background: #f8fafc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .gpupload-note {
        display: block;
        margin-top: 8px;
        color: #c62828;
        font-weight: bold;
        font-size: 13px;
    }

    .gpupload-btn {
        min-width: 110px;
        padding: 10px 18px;
        margin-right: 10px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .gpupload-btn-primary {
        background: #A92A61;
        color: #ffffff;
    }

    .gpupload-btn-secondary {
        background: #e6eef7;
        color: #1f4e79;
        border: 1px solid #b8cbe0;
    }

    .gpupload-status-message {
        margin-top: 18px;
        padding: 12px 14px;
        border: 1px solid #d7e1eb;
        background: #f8fafc;
        color: #1f2933;
        min-height: 20px;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .gpupload-page-wrap {
            width: calc(100% - 24px);
            margin: 20px auto;
        }

        .gpupload-card-body {
            padding: 18px;
        }

        .gpupload-form-table,
        .gpupload-form-table tbody,
        .gpupload-form-table tr,
        .gpupload-form-table td {
            display: block;
            width: 100%;
        }

        .gpupload-label-cell {
            text-align: left;
            padding-bottom: 4px;
        }

        .gpupload-colon-cell {
            display: none;
        }

        .gpupload-btn {
            width: 100%;
            margin: 0 0 10px;
        }
    }
</style>

<div class="gpupload-page-wrap">
    <div class="gpupload-message-panel" id="topMsg"></div>

    <div class="gpupload-upload-card">
        <div class="gpupload-card-head">Customer Gram Panchayat Upload</div>

        <div class="gpupload-card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <table class="gpupload-form-table">
                    <tr>
                        <td class="gpupload-label-cell">CSV File*</td>
                        <td class="gpupload-colon-cell">:</td>
                        <td>
                            <input type="file" name="csv_file" accept=".csv" required class="gpupload-file-input">
                            <span class="gpupload-note">[Extension will be .csv &nbsp;|&nbsp; Columns : customer_code, State, District, Block, Gram Panchayat]</span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="gpupload-colon-cell">&nbsp;</td>
                        <td>
                            <button type="submit" class="gpupload-btn gpupload-btn-primary">Upload</button>
                            <button type="button" class="gpupload-btn gpupload-btn-secondary" onclick="downloadCSV()">Download Sample CSV</button>
                        </td>
                    </tr>
                </table>
            </form>

            <div class="gpupload-status-message" id="msg"></div>
        </div>
    </div>
</div>

<script>
document.getElementById("uploadForm").onsubmit = function(e){
    e.preventDefault();

    var formData = new FormData(this);

    document.getElementById("msg").innerHTML = "Uploading, please wait...";
    document.getElementById("topMsg").innerHTML = "";

    fetch("customer_gram_panchayat_upload.php", {
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
    window.location.href = "customer_gram_panchayat_sample.php";
}
</script>
<?php
}
?>
