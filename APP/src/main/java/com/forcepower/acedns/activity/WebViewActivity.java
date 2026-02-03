package com.forcepower.acedns.activity;

import android.content.ActivityNotFoundException;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.net.http.SslError;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.webkit.SslErrorHandler;
import android.webkit.ValueCallback;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.Toast;

//import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.api.clients.RestClient;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class WebViewActivity extends FragmentActivity {

    Button mButtonBack;
    WebView webView;
    String val="";
    String url="https://salesleaderboard.starcement.co.in:8080/";
    private static final String TAG = "WebViewDebug";
    public Context mContext;
    String userName = "test@test.in";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        //EdgeToEdge.enable(this);
        //requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_web_view);

        mButtonBack = (Button) findViewById(R.id.back);
        mContext = WebViewActivity.this;

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        val = getIntent().getStringExtra("val");
        if(val.equalsIgnoreCase("Leader_Board")){
            url="https://salesleaderboard.starcement.co.in:8080/";
        }else if(val.equalsIgnoreCase("bd_leader_board")){
            url="https://bdsalesleaderboard.starcement.co.in:8081/";
        }else if(val.equalsIgnoreCase("manchtech")){
            if(val.equalsIgnoreCase("manchtech")){
                customer_orientation_upload(mContext);
                url="https://manchtech.com/login/";

                try {
                    Intent myIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                    startActivity(myIntent);
                    finish();
                    return;
                } catch (ActivityNotFoundException e) {
                    Toast.makeText(this, "No application can handle this request."
                            + " Please install a webbrowser",  Toast.LENGTH_LONG).show();
                    e.printStackTrace();
                    return;
                }
            }
        }

        webView = findViewById(R.id.web_view);

        //WebView webView = findViewById(R.id.webview);
        if (webView == null) {
            Log.e(TAG, "WebView is null");
            return;
        }

        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setBuiltInZoomControls(true);
        webSettings.setLoadWithOverviewMode(true);
        webSettings.setUseWideViewPort(true);
        webSettings.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                Log.d(TAG, "Page Loaded: " + url);
            }

        });

        webView.loadUrl(url);

    }

    public static boolean customer_orientation_upload(Context mContext) {
        Call<String> call = RestClient.getRestServiceString(mContext).customer_orientation_upload(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                //Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {



                    }catch (Exception e){

                    }

                }
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
            }
        });
        return true;
    }


}