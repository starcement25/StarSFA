package com.forcepower.acedns.activity;

import static android.view.View.VISIBLE;

import androidx.fragment.app.FragmentActivity;

import android.app.Dialog;
import android.app.DownloadManager;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerBeatAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

public class DsrPdfActivity extends FragmentActivity {

    Button mButtonBack,mButtonDownload,buttonDownload_show,buttonDownload_share,buttonDistributor;

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;
    DownloadManager manager;
    TextView txtpdf;

    private String filepath = "http://africau.edu/images/default/sample.pdf";
    private URL url = null;
    private String fileName;

    LinearLayout ll_distributoe;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dsr_pdf);


        mButtonBack = (Button) findViewById(R.id.back);
        mButtonDownload = (Button) findViewById(R.id.buttonDownload);
        buttonDownload_show = (Button) findViewById(R.id.buttonDownload_show);
        buttonDownload_share = (Button) findViewById(R.id.buttonDownload_share);
        buttonDistributor = (Button) findViewById(R.id.buttonDistributor);
        txtpdf = findViewById(R.id.txtpdf);
        mContext = DsrPdfActivity.this;
        ll_distributoe = (LinearLayout) findViewById(R.id.ll_distributoe);


        if (Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("distributor wise")) {
            ll_distributoe.setVisibility(View.VISIBLE);
        }else if (Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("yes")) {
            ll_distributoe.setVisibility(View.GONE);
        }

        filepath = BaseUrl.baseUrl + AceDnsWebServiceURL.dsr_summary_pdf
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

Log.d("_DOWNLOAD_", "_DOWNLOAD_ DsrPdfActivity: " + filepath);
        try {
            url = new URL(filepath);
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }

        fileName = url.getPath();


        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();

            }
        });

        buttonDistributor.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showDistributor();

            }
        });

        mButtonDownload.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                djob();
            }
        });

        buttonDownload_share.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                send();
            }
        });

        buttonDownload_show.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                //File file=new File(Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)+"/"+fileName);
                //Uri uri= FileProvider.getUriForFile(OrderSummaryPDFActivity.this,"org.forcepower.acedns"+".provider",file);

                Intent browserIntent = new Intent(Intent.ACTION_VIEW);
                browserIntent.setDataAndType(Uri.parse(filepath),"application/pdf");
                startActivity(browserIntent);

                /*Intent i=new Intent(Intent.ACTION_VIEW);
                i.setDataAndType(Uri.parse(filepath),"application/pdf");
                i.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP|Intent.FLAG_GRANT_READ_URI_PERMISSION);
                startActivity(i);*/
            }
        });
    }

    private void djob(){
        DownloadManager.Request request = new DownloadManager.Request(Uri.parse(url + ""));
        request.setTitle(fileName);
        request.setMimeType("applcation/pdf");
        request.allowScanningByMediaScanner();
        request.setAllowedOverMetered(true);
        request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
        request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName);
        DownloadManager dm = (DownloadManager) getSystemService(DOWNLOAD_SERVICE);
        dm.enqueue(request);
    }

    private void send(){
        try {
            File file=new File(Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)+"/"+fileName);
            Intent intentShare = new Intent(Intent.ACTION_SEND);
            intentShare.setType("application/pdf");
            intentShare.putExtra(Intent.EXTRA_STREAM, Uri.parse(""+file));
            startActivity(Intent.createChooser(intentShare, "Share the file ..."));
        }catch (Exception e){
            Utils.showToast(mContext,"Error to Share");
        }

    }
    Dialog stokistDialog;
    public void showDistributor() {
        final ArrayList<CustomerDetails> itemListBeforeSearch = mAceDnsDatabase.getDealerList();
        //final ArrayList<CustomerDetails> tempCustomerList = mAceDnsDatabase.getStokistRetailList(""+stockist_code,custType,custRouteCode);
        final ArrayList<CustomerDetails> itemList = new ArrayList<>(itemListBeforeSearch);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Dealer Found");
            //bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(DsrPdfActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.select_from_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please select Dealer");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final CustomerBeatAdapter adapter1 = new CustomerBeatAdapter(DsrPdfActivity.this, R.layout.customer_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);

            EditText searchText = (EditText) stokistDialog
                    .findViewById(R.id.autoCompleteTextView1);

            searchText.setVisibility(VISIBLE);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    //adapter1.getFilter().filter(s.toString());
                    String searchString = searchText.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    itemList.clear();
                    for (int i = 0; i < itemListBeforeSearch.size(); i++) {
                        String routeName = itemListBeforeSearch.get(i).getCustomerName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                itemList.add(itemListBeforeSearch.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    adapter1.notifyDataSetChanged();
                }
            });

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    stokistDialog.cancel();
                    CustomerDetails detailsObj = itemList.get(arg2);
                    String routeName = detailsObj.getCustomerName();
                    buttonDistributor.setText(routeName);

                }
            });


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            Button create_route = (Button) stokistDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            stokistDialog.show();
        } else {
            Utils.showToast(DsrPdfActivity.this,
                    "There is no predefined route");
        }

    }

}