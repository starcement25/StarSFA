package com.forcepower.acedns.api.clients;


import android.content.Context;

import com.forcepower.acedns.constants.Constants;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.converter.scalars.ScalarsConverterFactory;
import com.forcepower.acedns.constants.BaseUrl;


public class RestClient {

    public static final String BASE_URL = BaseUrl.baseUrl;

    public static RestService restService = null;

    public static RestService getRestServiceString(final Context context) {
        if (restService == null) {
            OkHttpClient.Builder httpClient = new OkHttpClient.Builder()
                    .callTimeout(1, TimeUnit.MINUTES)
                    .connectTimeout(20, TimeUnit.SECONDS)
                    .readTimeout(30, TimeUnit.SECONDS)
                    .writeTimeout(30, TimeUnit.SECONDS);

            Retrofit retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(ScalarsConverterFactory.create())
                    .client(httpClient.build())
                    .build();

            restService = retrofit.create(RestService.class);

        }
        return restService;
    }

}
