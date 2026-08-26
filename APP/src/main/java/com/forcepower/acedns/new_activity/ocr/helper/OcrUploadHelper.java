package com.forcepower.acedns.new_activity.ocr.helper;

import android.app.Activity;
import android.content.Context;
import android.net.Uri;
import android.util.Log;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Arrays;
import java.util.concurrent.TimeUnit;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class OcrUploadHelper {

    private static final String OCR_URL = "https://starocr.myvtd.site/extract-structured/?engine=aws";

    public interface OcrCallback {
        void onSuccess(JSONObject data);
        void onFailure(String error);
    }
    public static void uploadAndExtract(Context context, Uri imageUri, OcrCallback callback) {
        try {
            String mimeType = context.getContentResolver().getType(imageUri);
            if (mimeType == null) mimeType = "image/jpeg"; // fallback

            String ext = "jpeg";
            if (mimeType.contains("png")) ext = "png";
            else if (mimeType.contains("pdf")) ext = "pdf";
            else if (mimeType.contains("jpeg") || mimeType.contains("jpg")) ext = "jpeg";
            else {
                Log.d("uploadAndExtract", "onResponse: " + mimeType);
                // Not a type the server accepts — fail fast instead of uploading garbage
                callback.onFailure("Unsupported file type: " + mimeType);
                return;
            }

            File file = getFileFromUri(context, imageUri, ext);

            OkHttpClient client = new OkHttpClient.Builder()
                    .connectTimeout(120, TimeUnit.SECONDS)
                    .writeTimeout(120, TimeUnit.SECONDS)
                    .readTimeout(120, TimeUnit.SECONDS)
                    .build();

            RequestBody fileBody = RequestBody.create(
                    MediaType.parse(mimeType), file); // <-- real mime type, not octet-stream

            RequestBody body = new MultipartBody.Builder()
                    .setType(MultipartBody.FORM)
                    .addFormDataPart("file", file.getName(), fileBody)
                    .build();

            Request request = new Request.Builder()
                    .url(OCR_URL)
                    .method("POST", body)
                    .addHeader("accept", "application/json")
                    .build();

            client.newCall(request).enqueue(new Callback() {
                @Override
                public void onFailure(Call call, IOException e) {
                    ((Activity) context).runOnUiThread(() ->
                            callback.onFailure(e.getMessage() != null ? e.getMessage() : "Request failed"));
                }

                @Override
                public void onResponse(Call call, Response response) throws IOException {
                    String respBody = response.body() != null ? response.body().string() : "";
                    Log.d("TAG", "onResponse: "+respBody);
                    if (!response.isSuccessful()) {
                        ((Activity) context).runOnUiThread(() ->
                                callback.onFailure("Server error: " + response.code() + " - " + respBody));
                        return;
                    }
                    try {
                        JSONObject json = new JSONObject(respBody);
                        ((Activity) context).runOnUiThread(() -> callback.onSuccess(json));
                    } catch (JSONException e) {
                        ((Activity) context).runOnUiThread(() ->
                                callback.onFailure("Invalid JSON response"));
                    }
                }
            });

        } catch (Exception e) {
            callback.onFailure("Failed to prepare file: " + e.getMessage());
        }
    }

    // Copies content:// Uri into a real File OkHttp can read, using the correct extension
    private static File getFileFromUri(Context context, Uri uri, String ext) throws IOException {
        InputStream inputStream = context.getContentResolver().openInputStream(uri);
        String fileName = "ocr_upload_" + System.currentTimeMillis() + "." + ext;
        File tempFile = new File(context.getCacheDir(), fileName);

        try (OutputStream outputStream = new FileOutputStream(tempFile)) {
            byte[] buffer = new byte[4096];
            int bytesRead;
            while ((bytesRead = inputStream.read(buffer)) != -1) {
                outputStream.write(bytesRead == 4096 ? buffer : Arrays.copyOf(buffer, bytesRead));
            }
        }
        if (inputStream != null) inputStream.close();
        return tempFile;
    }
}