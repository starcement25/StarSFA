package com.forcepower.acedns.util;

import android.content.ContentValues;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.util.Log;

import java.io.IOException;
import java.io.InputStream;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import okhttp3.FormBody;
import okhttp3.HttpUrl;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

/**
 * Created by Suvradip on 24/10/2017.
 */

public class HttpCalling {
    public static final MediaType JSON = MediaType.parse("application/json; charset=utf-8");
    public static final MediaType XML = MediaType.parse("application/xml; charset=utf-8");
    OkHttpClient client;

    public HttpCalling() {
        client = new OkHttpClient();
    }

    private static OkHttpClient getUnsafeOkHttpClient() {
        try {
            TrustManager[] trustAllCerts = new TrustManager[]{
                    new X509TrustManager() {
                        public void checkClientTrusted(java.security.cert.X509Certificate[] chain, String authType) {}
                        public void checkServerTrusted(java.security.cert.X509Certificate[] chain, String authType) {}
                        public java.security.cert.X509Certificate[] getAcceptedIssuers() { return new java.security.cert.X509Certificate[]{}; }
                    }
            };

            SSLContext sslContext = SSLContext.getInstance("SSL");
            sslContext.init(null, trustAllCerts, new java.security.SecureRandom());

            OkHttpClient.Builder builder = new OkHttpClient.Builder();
            builder.sslSocketFactory(sslContext.getSocketFactory(), (X509TrustManager)trustAllCerts[0]);
            builder.hostnameVerifier((hostname, session) -> true);

            builder.connectTimeout(30, TimeUnit.SECONDS);
            builder.readTimeout(60, TimeUnit.SECONDS);
            builder.writeTimeout(30, TimeUnit.SECONDS);
            builder.callTimeout(120, TimeUnit.SECONDS);

            return builder.build();
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    public static boolean isConnectionPossible(Context mContext) {
        ConnectivityManager cm = (ConnectivityManager) mContext.getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return (netInfo != null && netInfo.isConnected());
    }

    public static String httpGetCallWithXmlResponse(String url, ContentValues values) {
        OkHttpClient client = new OkHttpClient.Builder()
                .connectTimeout(50, TimeUnit.SECONDS)
                .writeTimeout(50, TimeUnit.SECONDS)
                .readTimeout(50, TimeUnit.SECONDS)
                .build();
        String responseFromServer = "";
        HttpUrl.Builder urlBuilder = HttpUrl.parse(url).newBuilder();
        if (values != null) {
            for (Map.Entry<String, Object> entry : values.valueSet()) {
                String key = entry.getKey();
                String value = entry.getValue().toString();
                urlBuilder.addQueryParameter(key, value);
            }
            url = urlBuilder.build().toString();
        }
        Log.d("TAG", "_DOWNLOAD_survey_form_details: "+url);
        try {
            Request request = new Request.Builder()
                    .url(url)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_survey_form_details: "+e.toString());
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public static String httpGetCallWithXmlResponseAndTimeOutParam(String url, ContentValues values,int connectTimeOut,int writeTimeOut,int readTimeOut) {
        OkHttpClient client = new OkHttpClient.Builder()
                .connectTimeout(connectTimeOut, TimeUnit.SECONDS)
                .writeTimeout(writeTimeOut, TimeUnit.SECONDS)
                .readTimeout(readTimeOut, TimeUnit.SECONDS)
                .build();
        String responseFromServer = "";
        HttpUrl.Builder urlBuilder = HttpUrl.parse(url).newBuilder();
        if (values != null) {
            for (Map.Entry<String, Object> entry : values.valueSet()) {
                String key = entry.getKey();
                String value = entry.getValue().toString();
                urlBuilder.addQueryParameter(key, value);
            }
            url = urlBuilder.build().toString();
        }
        try {
            Request request = new Request.Builder()
                    .url(url)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (IOException e) {
            responseFromServer="Network Failure";
        }
        return responseFromServer;
    }

    public InputStream httpGetCallWithInputStreamResponse(String url) {
        InputStream resp = null;
        try {
            Request request = new Request.Builder()
                    .url(url)
                    .build();
            Response response = client.newCall(request).execute();
            resp = response.body().byteStream();
        } catch (Exception e) {
            e.printStackTrace();
            resp = null;
        }
        return resp;
    }

    public static String httpGetCallWithTextResponse(String url){
        String responseFromServer = "";
        try {
        OkHttpClient client = getUnsafeOkHttpClient();
        Request request = new Request.Builder()
                .url(url)
                .get()
                .build();
        Response response = null;

            response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (IOException e) {
            Log.d("TAG", "_DOWNLOAD_ httpGetCallWithTextResponse: "+e.getMessage()+" : "+url);
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public static String httpGetCall(String url) {
        String responseFromServer = "";

        OkHttpClient client = new OkHttpClient().newBuilder()
                .build();
//        MediaType mediaType = MediaType.parse("text/plain");
//        RequestBody body = RequestBody.create(mediaType, "");
        Request request = new Request.Builder()
                .url(url)
                .build();


        try {
            Response response = client.newCall(request).execute();
            if (response.body() != null) {
                responseFromServer = response.body().string();
            } else {
                responseFromServer = "Empty Response";
            }
        } catch (IOException e) {
            Log.d("TAG", "_DOWNLOAD_ httpGetCallWithTextResponse: " + e.getMessage());
            responseFromServer = "Network Failure";
        }

        return responseFromServer;
    }

    public static String httpPostCallWithXmlBodyXmlResponseDecrypted(String url, String xml) {
        String responseFromServer = "";
        try {
            OkHttpClient client = new OkHttpClient.Builder()
                    .readTimeout(50, TimeUnit.SECONDS)
                    .build();
            RequestBody body = RequestBody.create(XML, xml);
            Request request = new Request.Builder()
                    .url(url)
                    .post(body)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (Exception e) {
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public static String httpPostCallWithXmlResponse(String url, ContentValues values) {
        OkHttpClient client = new OkHttpClient();
        String responseFromServer = "";
        FormBody.Builder formBuilder = new FormBody.Builder();
        if (values != null) {
            for (Map.Entry<String, Object> entry : values.valueSet()) {
                String key = entry.getKey(); // name
                String value = entry.getValue().toString(); // value
                formBuilder.add(key, value);
            }
        }
        try {
            RequestBody formBody = formBuilder.build();
            Request request = new Request.Builder()
                    .url(url)
                    .post(formBody)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (Exception e) {
            e.printStackTrace();
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public String httpPostCallWithXmlResponseDecrypted(String url, String json) {
        String responseFromServer = "";
        try {
            RequestBody body = RequestBody.create(JSON, json);
            Request request = new Request.Builder()
                    .url(url)
                    .post(body)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
            MCrypt mcrypt = new MCrypt();
            responseFromServer = new String(mcrypt.decrypt(responseFromServer));
        } catch (Exception e) {
            e.printStackTrace();
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public static String httpPostCallWithDecrypted(String url, String json) {
        String responseFromServer = "";
        try {
            OkHttpClient client = getUnsafeOkHttpClient();
            RequestBody body = RequestBody.create(JSON, json);
            Request request = new Request.Builder()
                    .url(url)
                    .post(body)
                    .build();
            Response response = null;

            response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (IOException e) {
            Log.d("TAG", "_DOWNLOAD_ httpGetCallWithTextResponse: "+e.getMessage());
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }

    public static String httpPostCallWithXmlBodyJsonResponseDecrypted(String url, String json) {
        String responseFromServer = "";
        try {
            OkHttpClient client = new OkHttpClient.Builder()
                    .readTimeout(250, TimeUnit.SECONDS)
                    .build();
            RequestBody body = RequestBody.create(JSON, json);
            Request request = new Request.Builder()
                    .url(url)
                    .post(body)
                    .build();
            Response response = client.newCall(request).execute();
            responseFromServer = response.body().string();
        } catch (Exception e) {
            e.printStackTrace();
            responseFromServer = "Network Failure";
        }
        return responseFromServer;
    }
}
