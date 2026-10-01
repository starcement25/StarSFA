package com.forcepower.acedns.new_activity.market_feedback.api_connect;

import android.os.Handler;
import android.os.Looper;
import android.util.Log;

import com.forcepower.acedns.constants.Constants;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.URLEncoder;
import java.util.concurrent.TimeUnit;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class GramPanchayatApproveApi {

    private static final String BASE_URL = "https://sfa.starcement.co.in/misreport/approve_gram_panchayat.php";

    private static final OkHttpClient client = new OkHttpClient.Builder()
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .build();

    public interface ApproveCallback {
        void onSuccess(String message, JSONObject data);
        void onFailure(String errorMessage);
    }

    public static void updateStatus(String customerCode, String status, ApproveCallback callback) {
        Handler mainHandler = new Handler(Looper.getMainLooper());

        String url;
        try {
            url = BASE_URL
                    + "?customer_code=" + URLEncoder.encode(customerCode, "UTF-8")
                    + "&status=" + URLEncoder.encode(status, "UTF-8")
                    + "&emp_code=" + URLEncoder.encode(Constants.employeeDetailObject.getEmpCode(), "UTF-8");
        } catch (Exception e) {
            mainHandler.post(() -> callback.onFailure("URL encode error: " + e.getMessage()));
            return;
        }

        Log.d("TAG", "_APPROVE_API_ url: " + url);

        Request request = new Request.Builder()
                .url(url)
                .get()
                .build();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(Call call, IOException e) {
                Log.d("TAG", "_APPROVE_API_ onFailure: " + e.getMessage());
                mainHandler.post(() -> callback.onFailure(e.getMessage()));
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException {
                String body = response.body() != null ? response.body().string() : "";
                Log.d("TAG", "_APPROVE_API_ onResponse: code=" + response.code() + " body=" + body);

                if (!response.isSuccessful()) {
                    mainHandler.post(() -> callback.onFailure("HTTP " + response.code()));
                    return;
                }

                try {
                    JSONObject json = new JSONObject(body);
                    String status = json.optString("status", "");
                    String message = json.optString("message", "");
                    JSONObject data = json.optJSONObject("data");

                    if ("success".equalsIgnoreCase(status)) {
                        mainHandler.post(() -> callback.onSuccess(message, data));
                    } else {
                        mainHandler.post(() -> callback.onFailure(message.isEmpty() ? "Approval failed" : message));
                    }
                } catch (JSONException e) {
                    Log.d("TAG", "_APPROVE_API_ JSON parse error: " + e.getMessage());
                    mainHandler.post(() -> callback.onFailure("Invalid response from server"));
                }
            }
        });
    }
}
