package com.forcepower.acedns.api.clients;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import org.json.JSONObject;

import java.io.IOException;

public class TRANS_Emp_level {

    private static final String BASE_URL = "https://sfa.starcement.co.in/emp_level.php?emp_code=";
    private static final OkHttpClient client = new OkHttpClient();

    public interface EmpLevelCallback {
        void onResult(String level);
        void onError(String error);
    }

    public static void fetchLevel(String empCode, EmpLevelCallback callback) {
        Request request = new Request.Builder()
                .url(BASE_URL + empCode)
                .get()
                .build();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(Call call, IOException e) {
                callback.onError(e.getMessage());
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException {
                if (!response.isSuccessful() || response.body() == null) {
                    callback.onError("HTTP " + response.code());
                    return;
                }
                try {
                    String body = response.body().string();
                    JSONObject json = new JSONObject(body);
                    boolean status = json.optBoolean("status", false);
                    if (status) {
                        callback.onResult(json.optString("level", null));
                    } else {
                        callback.onError(json.optString("message", "Employee not found"));
                    }
                } catch (Exception e) {
                    callback.onError("Parse error: " + e.getMessage());
                }
            }
        });
    }
}