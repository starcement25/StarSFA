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
    .competitor-page-wrap {
        width: 70%;
        max-width: 900px;
        margin: 28px auto;
        color: #243447;
        font-family: Arial, sans-serif;
    }

    .competitor-message-panel {
        margin-bottom: 18px;
        text-align: center;
        color: #c62828;
        font-weight: bold;
        min-height: 24px;
    }

    .competitor-upload-card {
        background: #ffffff;
        border: 1px solid #cfd8e3;
        box-shadow: 0 10px 28px rgba(36, 52, 71, 0.08);
    }

    .competitor-card-head {
        padding: 14px 18px;
        background: linear-gradient(90deg, #A92A61 , #A92A61);
        color: #ffffff;
        font-size: 18px;
        font-weight: bold;
    }

    .competitor-card-body {
        padding: 24px 28px 28px;
    }

    .competitor-form-table {
        width: 100%;
        border-collapse: collapse;
    }

    .competitor-form-table td {
        padding: 10px 6px;
        vertical-align: middle;
    }

    .competitor-label-cell {
        width: 180px;
        text-align: right;
        font-weight: bold;
        color: #334e68;
    }

    .competitor-colon-cell {
        width: 16px;
        text-align: center;
        color: #334e68;
    }

    .competitor-file-input {
        width: 100%;
        max-width: 360px;
        padding: 10px;
        border: 1px solid #c5d0db;
        background: #f8fafc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .competitor-note {
        display: block;
        margin-top: 8px;
        color: #c62828;
        font-weight: bold;
        font-size: 13px;
    }

    .competitor-actions {
        padding-top: 8px;
    }

    .competitor-btn {
        min-width: 110px;
        padding: 10px 18px;
        margin-right: 10px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .competitor-btn-primary {
        background: #A92A61 ;
        color: #ffffff;
    }

    .competitor-btn-secondary {
        background: #e6eef7;
        color: #1f4e79;
        border: 1px solid #b8cbe0;
    }

    .competitor-status-message {
        margin-top: 18px;
        padding: 12px 14px;
        border: 1px solid #d7e1eb;
        background: #f8fafc;
        color: #1f2933;
        min-height: 20px;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .competitor-page-wrap {
            width: calc(100% - 24px);
            margin: 20px auto;
        }

        .competitor-card-body {
            padding: 18px;
        }

        .competitor-form-table,
        .competitor-form-table tbody,
        .competitor-form-table tr,
        .competitor-form-table td {
            display: block;
            width: 100%;
        }

        .competitor-label-cell {
            text-align: left;
            padding-bottom: 4px;
        }

        .competitor-colon-cell {
            display: none;
        }

        .competitor-btn {
            width: 100%;
            margin: 0 0 10px;
        }
    }
</style>

<div class="competitor-page-wrap">
    <div class="competitor-message-panel" id="topMsg"></div>

    <div class="competitor-upload-card">
        <div class="competitor-card-head">Upload Competitor CSV</div>

        <div class="competitor-card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <table class="competitor-form-table">
                    <tr>
                        <td class="competitor-label-cell">CSV File*</td>
                        <td class="competitor-colon-cell">:</td>
                        <td>
                            <input type="file" name="csv_file" accept=".csv" required class="competitor-file-input">
                            <span class="competitor-note">[Extension will be .csv]</span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="competitor-colon-cell">&nbsp;</td>
                        <td class="competitor-actions">
                            <button type="submit" class="competitor-btn competitor-btn-primary">Upload</button>
                            <button type="button" class="competitor-btn competitor-btn-secondary" onclick="downloadCSV()">Download Sample CSV</button>
                        </td>
                    </tr>
                </table>
            </form>

            <div class="competitor-status-message" id="msg"></div>
        </div>
    </div>
</div>

<script>
document.getElementById("uploadForm").onsubmit = function(e){
    e.preventDefault();

    var formData = new FormData(this);

    fetch("upload_csv.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("msg").innerHTML = data.message;
        document.getElementById("topMsg").innerHTML = data.message;
    });
};

function downloadCSV(){
    window.location.href = "download_sample.php";
}
</script>
<?php
}
?>
