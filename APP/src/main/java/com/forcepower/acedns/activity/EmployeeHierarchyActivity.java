package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.Gravity;
import android.view.Window;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.R.color;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.List;

public class EmployeeHierarchyActivity extends AceDnsParentActivity {

    public static LinearLayout mParentLayout = null;
    public String[] values1;
    public String[] values2;
    public String[] values3;
    public String[] values4;
    public String[] values5;
    public String[] values6;
    public String[] values7;
    public String[] values8;
    public ArrayList<Button> mButtonList;
    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mPrepareEmployeeProgressDialog;
    public Handler mPrepareEmployeeHandler;
    public Context mContext;
    List<String> list = new ArrayList<String>();
    int max = 0;
    int max10 = 0;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_employee_hierarchy);
        RegisterActivities.registerActivity(this);

        mContext = EmployeeHierarchyActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mButtonList = new ArrayList<Button>();
        InitializeView();

        mPrepareEmployeeHandler = new Handler() {
            public void handleMessage(Message msg) {
                mPrepareEmployeeProgressDialog.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                EmployeeHierarchyActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                for (int count1 = 0; count1 < values1.length; count1++) {
                                    DrawLayout(values1[count1], 0, count1 + 1);

                                    for (int count2 = 0; count2 < values2.length; count2++) {
                                        DrawLayout(values2[count2], 1, count2 + 1);

                                        for (int count3 = 0; count3 < values3.length; count3++) {
                                            DrawLayout(values3[count3], 2, count3 + 1);

                                            for (int count4 = 0; count4 < values4.length; count4++) {
                                                DrawLayout(values4[count4], 3, count4 + 1);

                                                for (int count5 = 0; count5 < values5.length; count5++) {
                                                    DrawLayout(values5[count5], 4, count5 + 1);

                                                    for (int count6 = 0; count6 < values6.length; count6++) {
                                                        DrawLayout(values6[count5], 5, count6 + 1);

                                                        for (int count7 = 0; count7 < values7.length; count7++) {
                                                            DrawLayout(values7[count7], 6, count7 + 1);

                                                            for (int count8 = 0; count8 < values8.length; count8++) {
                                                                DrawLayout(values8[count8], 7, count8 + 1);

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }
                                    }

                                }
                                break;

                        }
                    }
                });
            }
        };

        PrepareHierarchyData(1, Constants.employeeDetailObject.getEmpCode());


    }

    public void InitializeView() {
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(EmployeeHierarchyActivity.this) + "~"
                + Utils.getDBVersion(EmployeeHierarchyActivity.this));
        mParentLayout = (LinearLayout) findViewById(R.id.linearLayoutParent);

    }

    @SuppressWarnings("deprecation")
    public void DrawLayout(String name, int paddingleft, int hierarchy) {
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        LinearLayout childlayout = new LinearLayout(this);
        childlayout.setLayoutParams(params);
        childlayout.setPadding(paddingleft, 0, 0, 2);
        childlayout.setOrientation(LinearLayout.HORIZONTAL);

        childlayout.addView(NewtextView(String.valueOf(hierarchy) + "." + name));


        LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(
                250, LayoutParams.WRAP_CONTENT, 1);
        LinearLayout buttonLayout = new LinearLayout(mContext);
        buttonLayout.setLayoutParams(buttonLayoutParams);
        Button button = new Button(mContext);
        LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        button.setLayoutParams(buttonParams);
        button.setTag(name);
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
        button.setText("Allocate");
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
        button.setOnClickListener(this);
        buttonLayout.addView(button);
        childlayout.addView(buttonLayout);
        mButtonList.add(button);
        mParentLayout.addView(childlayout);
    }


    private TextView NewtextView(String name) {
        LinearLayout.LayoutParams Params = new LinearLayout.LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
        TextView textView = new TextView(this);
        textView.setLayoutParams(Params);
        textView.setText(name);
        textView.setTextColor(color.blue);
        return textView;
    }


    public void PrepareHierarchyData(final int task, final String params) {
        mPrepareEmployeeProgressDialog = new ProgressDialog(mContext);
        mPrepareEmployeeProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareEmployeeProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {

                    case 1:
                        //int length=0;
                        max = mAceDnsDatabase.GetHierarchywiseEmployee(params);
                        values1 = new String[max];
                        for (int i = 0; i < Constants.mEmployeeList.length; i++) {
                            values1[i] = Constants.mEmployeeList[i];
                        }

                        break;


                }
                Message msg = mPrepareEmployeeHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mPrepareEmployeeHandler.sendMessage(msg);
            }
        }.start();
    }

}
