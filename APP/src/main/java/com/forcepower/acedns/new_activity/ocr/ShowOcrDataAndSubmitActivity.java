package com.forcepower.acedns.new_activity.ocr;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Looper;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.FileProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.ActivitySurveyLanding;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.ocr.adapter.OcrDataItemAdapter;
import com.forcepower.acedns.new_activity.ocr.dataset.OcrItem;
import com.forcepower.acedns.new_activity.ocr.helper.OcrUploadHelper;
import com.forcepower.acedns.new_activity.sitelead.NewSiteLeadActivity;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Locale;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class ShowOcrDataAndSubmitActivity extends AceDnsParentActivity implements View.OnClickListener {

    Context mContext;
    private Button backButton;
    private Button addButton;
    private RecyclerView ocrDataList;
    private Button saveButton;
    private Button downloadButton; // NEW: exports the table below as CSV

    private TextView dealerSubDealerNameText, addressText, areaLocationText, techMeetTypeText, dateText;

    ArrayList<OcrItem> list = new ArrayList<>();
    OcrDataItemAdapter adapter;
    ProgressDialog progressDialog;

    private ArrayList<Uri> imageUriList = new ArrayList<>();
    private int currentImageIndex = 0;
    private int runningSlNo = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_show_ocr_data_and_submit);

        mContext = ShowOcrDataAndSubmitActivity.this;
        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
        if (v == saveButton) {
            submitOcrData();
        }
        if (v == addButton) {
            addNewItem();
        }
        if (v == downloadButton) {
            exportListToCsv();
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        saveButton = findViewById(R.id.saveButton);
        ocrDataList = findViewById(R.id.ocrDataList);
        addButton = findViewById(R.id.addButton);
        downloadButton = findViewById(R.id.downloadButton); // add this Button in your XML

        dealerSubDealerNameText = findViewById(R.id.dealerSubDealerNameText);
        addressText = findViewById(R.id.addressText);
        areaLocationText = findViewById(R.id.areaLocationText);
        techMeetTypeText = findViewById(R.id.techMeetTypeText);
        dateText = findViewById(R.id.dateText);

        backButton.setOnClickListener(this);
        addButton.setOnClickListener(this);
        saveButton.setOnClickListener(this);
        downloadButton.setOnClickListener(this);

        dealerSubDealerNameText.setText(getIntent().getStringExtra("customer_name"));
        addressText.setText(getIntent().getStringExtra("branch_name"));
        areaLocationText.setText(getIntent().getStringExtra("area_address"));
        techMeetTypeText.setText(getIntent().getStringExtra("tech_meet_type"));
        dateText.setText(getIntent().getStringExtra("date"));

        imageUriList = getIntent().getParcelableArrayListExtra("image");
        if (imageUriList == null) imageUriList = new ArrayList<>();

        showInList();
        requestForImageOcr();
    }

    private void showInList() {
        ocrDataList.setLayoutManager(new LinearLayoutManager(mContext));
        adapter = new OcrDataItemAdapter(mContext, list, new OcrDataItemAdapter.OnActionClickListener() {
            @SuppressLint("NotifyDataSetChanged")
            @Override
            public void deleteItem(OcrItem item, int position) {
                list.remove(position);
                renumberList();
                adapter.notifyDataSetChanged();
            }
        });
        ocrDataList.setAdapter(adapter);
    }

    private void renumberList() {
        int slNo = 1;
        for (OcrItem item : list) {
            if (!item.getHeader()) {
                item.setSlNo(String.valueOf(slNo));
                slNo++;
            }
        }
    }

    private void addNewItem() {
        OcrItem o = new OcrItem();
        o.setHeader(false);
        o.setName("");
        o.setPhoneNo("");
        o.setAddress("");
        o.setIlpRegistered("");
        list.add(o);
        renumberList();
        adapter.notifyItemInserted(list.size() - 1);
        ocrDataList.scrollToPosition(list.size() - 1);
    }

    private void submitOcrData() {
        ArrayList<OcrItem> validItems = new ArrayList<>();
        for (OcrItem item : list) {
            if (item.getHeader()) continue;
            if (item.getName() == null || item.getName().trim().isEmpty()
                    || item.getPhoneNo() == null || item.getPhoneNo().trim().isEmpty()) {
                Toast.makeText(mContext, "Sl No " + item.getSlNo() + ": name/phone can't be empty", Toast.LENGTH_SHORT).show();
                return;
            }
            validItems.add(item);
        }
        JSONArray jsonArray = new JSONArray();
        try {
            for (OcrItem item : validItems) {
                JSONObject obj = new JSONObject();
                obj.put("date", getIntent().getStringExtra("date"));
                obj.put("participant_name", item.getName().trim());
                obj.put("contact_no", item.getPhoneNo().trim());
                obj.put("address", item.getAddress().trim());
                obj.put("ilp_registered", item.getIlpRegistered().trim().toUpperCase());
                jsonArray.put(obj);
            }
        } catch (JSONException e) {
            e.printStackTrace();
            return;
        }
        JSONObject payload = new JSONObject();
        try {
            payload.put("participants", jsonArray);
            payload.put("date", getIntent().getStringExtra("date"));
            payload.put("tech_meet_type", getIntent().getStringExtra("tech_meet_type"));
            payload.put("address", getIntent().getStringExtra("area_address"));
            payload.put("branch_code", getIntent().getStringExtra("branch_code"));
            payload.put("dealer_code", getIntent().getStringExtra("customer_code"));
            payload.put("employee_code", Constants.employeeDetailObject.getEmpCode());
        } catch (JSONException e) {
            e.printStackTrace();
            return;
        }

        Log.d("TAG", "submitOcrData: "+payload);
        progressDialogOpen("Uploading data to server...");
        new Thread(() -> {
            try {

                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, payload.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_store_ocr_data.php")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();

                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no"))
                            ((ShowOcrDataAndSubmitActivity) mContext).showError(jsonObject.optString("error"));
                        else
                            ((ShowOcrDataAndSubmitActivity) mContext).showSuccess();
                    } catch (Exception e) {
                        ((ShowOcrDataAndSubmitActivity) mContext).showError(e.getMessage());
                    }
                }
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();

        // TODO: send `payload` to your submit API
    }
    private void showError(String message) {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, message, Toast.LENGTH_LONG).show());
    }

    private void showSuccess() {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, "OCR Data save successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(() -> {
            Intent intent = new Intent(mContext, ActivitySurveyLanding.class);
            intent.putExtra("SURVEYSUBMENUDETAILS", getIntent().getStringExtra("SURVEYSUBMENUDETAILS"));
            startActivity(intent);
            finish();
        }, 2000);
    }
    // ---------------- CSV EXPORT ----------------

    /**
     * Builds a CSV matching the printed "List of Participants" sheet:
     * header info block, then a table of Sl No / Name / Address / Contact No /
     * ILP registered / Signature (Signature left blank — it's filled by hand on paper).
     */
    private void exportListToCsv() {
        if (list.isEmpty()) {
            Toast.makeText(mContext, "No data to export", Toast.LENGTH_SHORT).show();
            return;
        }

        StringBuilder csv = new StringBuilder();
        csv.append("List of Participants\n\n");
        csv.append("Name of Dealer / Sub Dealer,").append(csvEscape(safeText(dealerSubDealerNameText))).append("\n");
        csv.append("Address,").append(csvEscape(safeText(addressText))).append("\n");
        csv.append("Area / Location,").append(csvEscape(safeText(areaLocationText))).append("\n");
        csv.append("Tech Meet type,").append(csvEscape(safeText(techMeetTypeText))).append("\n");
        csv.append("Date,").append(csvEscape(safeText(dateText))).append("\n\n");

        csv.append("Sl. No.,Name of Participants,Address,Contact No,ILP registered (Yes/No),Signature\n");
        for (OcrItem item : list) {
            if (item.getHeader()) continue;
            csv.append(csvEscape(item.getSlNo())).append(",");
            csv.append(csvEscape(item.getName())).append(",");
            csv.append(csvEscape(item.getAddress())).append(",");
            csv.append(csvEscape(item.getPhoneNo())).append(",");
            csv.append(csvEscape(item.getIlpRegistered())).append(",");
            csv.append(""); // Signature left blank
            csv.append("\n");
        }

        String fileName = "Participants_" + new SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(new java.util.Date()) + ".csv";

        Uri savedUri = saveCsvToDownloads(fileName, csv.toString());
        if (savedUri != null) {
            Toast.makeText(mContext, "Saved to Downloads: " + fileName, Toast.LENGTH_LONG).show();
            shareCsv(savedUri);
        } else {
            Toast.makeText(mContext, "Failed to save CSV", Toast.LENGTH_SHORT).show();
        }
    }

    private String safeText(TextView tv) {
        return tv != null && tv.getText() != null ? tv.getText().toString() : "";
    }

    private String csvEscape(String value) {
        if (value == null) return "";
        if (value.contains(",") || value.contains("\"") || value.contains("\n")) {
            return "\"" + value.replace("\"", "\"\"") + "\"";
        }
        return value;
    }

    /**
     * Writes the CSV to the public Downloads folder.
     * API 29+ uses MediaStore (scoped storage); below that, writes directly to the Downloads dir.
     */
    private Uri saveCsvToDownloads(String fileName, String content) {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                ContentValues values = new ContentValues();
                values.put(MediaStore.Downloads.DISPLAY_NAME, fileName);
                values.put(MediaStore.Downloads.MIME_TYPE, "text/csv");
                values.put(MediaStore.Downloads.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS);

                Uri collection = MediaStore.Downloads.EXTERNAL_CONTENT_URI;
                Uri itemUri = getContentResolver().insert(collection, values);
                if (itemUri == null) return null;

                try (OutputStream out = getContentResolver().openOutputStream(itemUri)) {
                    if (out == null) return null;
                    out.write(content.getBytes());
                }
                return itemUri;
            } else {
                File downloadsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS);
                if (!downloadsDir.exists()) downloadsDir.mkdirs();
                File file = new File(downloadsDir, fileName);
                try (FileOutputStream out = new FileOutputStream(file)) {
                    out.write(content.getBytes());
                }
                return FileProvider.getUriForFile(mContext, mContext.getPackageName() + ".provider", file);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    private void shareCsv(Uri uri) {
        Intent shareIntent = new Intent(Intent.ACTION_SEND);
        shareIntent.setType("text/csv");
        shareIntent.putExtra(Intent.EXTRA_STREAM, uri);
        shareIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
        startActivity(Intent.createChooser(shareIntent, "Share CSV"));
    }

    // ---------------- OCR PIPELINE (unchanged) ----------------

    private void progressDialogOpen(String title) {
        if (progressDialog == null) {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setCancelable(false);
        }
        progressDialog.setMessage(title);
        if (!progressDialog.isShowing()) {
            progressDialog.show();
        }
    }

    private void progressDialogClose() {
        ((ShowOcrDataAndSubmitActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }

    private void requestForImageOcr() {
        if (imageUriList.isEmpty()) {
            Toast.makeText(mContext, "No image found to process", Toast.LENGTH_SHORT).show();
            return;
        }
        currentImageIndex = 0;
        runningSlNo = 1;
        list.clear();
        processNextImage();
    }

    private void processNextImage() {
        if (currentImageIndex >= imageUriList.size()) {
            progressDialogClose();
            return;
        }

        Uri currentUri = imageUriList.get(currentImageIndex);
        progressDialogOpen("Please wait... OCR is processing image "
                + (currentImageIndex + 1) + " of " + imageUriList.size() + "...");

        OcrUploadHelper.uploadAndExtract(mContext, currentUri, new OcrUploadHelper.OcrCallback() {
            @Override
            public void onSuccess(JSONObject data) {
                appendOcrResponse(data);
                currentImageIndex++;
                processNextImage();
            }

            @Override
            public void onFailure(String error) {
                Toast.makeText(mContext, "OCR failed on image " + (currentImageIndex + 1) + ": " + error, Toast.LENGTH_LONG).show();
                currentImageIndex++;
                processNextImage();
            }
        });
    }

    @SuppressLint("NotifyDataSetChanged")
    private void appendOcrResponse(JSONObject data) {
        try {
            JSONArray participants = data.optJSONArray("participants");
            if (participants == null) {
                return;
            }

            for (int i = 0; i < participants.length(); i++) {
                JSONObject obj = participants.getJSONObject(i);
                if (!safeString(obj, "name", "").isEmpty()
                        || !safeString(obj, "contact_no", "").isEmpty()
                        || !safeString(obj, "address", "").isEmpty()
                        || !safeString(obj, "ilpregister", "").isEmpty()) {
                    OcrItem o = new OcrItem();
                    o.setHeader(false);
                    o.setSlNo(String.valueOf(runningSlNo));
                    o.setName(safeString(obj, "name", ""));
                    o.setPhoneNo(safeString(obj, "contact_no", ""));
                    o.setAddress(safeString(obj, "address", ""));
                    o.setIlpRegistered(safeString(obj, "ilpregister", ""));
                    list.add(o);
                    runningSlNo++;
                }
            }

            adapter.notifyDataSetChanged();

        } catch (Exception e) {
            Toast.makeText(mContext, "Error parsing OCR data", Toast.LENGTH_SHORT).show();
        }
    }

    private String safeString(JSONObject obj, String key, String defaultValue) {
        if (!obj.has(key) || obj.isNull(key)) return defaultValue;
        return obj.optString(key, defaultValue);
    }
}