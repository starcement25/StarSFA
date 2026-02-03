package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.InputType;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitTDAllocation;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.TDAllocation;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public class TDAllocationActivity extends AceDnsParentActivity {

    /*
     * Layout Views
     */
    public static ImageView mImageViewHeaderLogo		= null;
    public static LinearLayout mParentLayout 			= null;
    public static Button mButtonBack					= null;
    public static Button mButtonNoAlloocation			= null;
    public static Button mButtonSelectEmployee			= null;
    public static Button mButtonSubmit					= null;
    private List<EditText> mEditTextList;
    private AceDnsTransactionDatabase 	mAceDnsTransactionDatabase;
    private AceDnsDatabase 				mAceDnsDatabase;
    public Context mContext;
    public boolean isBoss			=false;
    public String currentParentEmployee		 ="";
    public String mAllocation		 ="";
    public String mOwnAllocation	 ="";
    public String mCurrentEmployeeName	="";
    public String mParentEmpCode	 	="";
    public String mType					="";

    ProgressDialog mProgressDialogSaudaAllocation;
    Handler mHandlerSaudaAllocation;

    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;
    EmployeeMasterDetails mEmployeeMasterDetails;
    ConnectionDetector cd;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activty_td_allocation_hierachywise);
        RegisterActivities.registerActivity(this);

        mContext = TDAllocationActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        cd= new ConnectionDetector(mContext);
        //IS CURRENT LOGGED IN EMPLOYEE BOSS
        isBoss=mAceDnsDatabase.CheckBoss(Constants.employeeDetailObject.getEmpCode());
        InitializeView();

        mEditTextList = new ArrayList<>();
        mParentEmpCode=Constants.employeeDetailObject.getEmpCode();

        mHandlerSaudaAllocation = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogSaudaAllocation.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                TDAllocationActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo)
                        {
                            case 1:
                                ShowEmployee();
                                break;
                            case 2:
                                if(Constants.mTDAllocationList!=null && Constants.mTDAllocationList.size()>0)
                                {
                                    DrawView();
                                }
                                else
                                {
                                    mParentLayout.removeAllViews();
                                    Utils.showToast(mContext, "No product found");
                                }
                                break;
                            case 3:
                                new TRANS_SubmitTDAllocation(mContext,true).execute();
                                break;
                            case 4:

                                break;
                        }
                    }
                });
            }
        };

        PrepareSaudaAccessData(4,"");
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    public void InitializeView() {
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        mButtonNoAlloocation = (Button) findViewById(R.id.no_ordr);
        mButtonNoAlloocation.setVisibility(View.GONE);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(TDAllocationActivity.this);

        mButtonSubmit = (Button) findViewById(R.id.buttonSubmit);
        mButtonSubmit.setOnClickListener(TDAllocationActivity.this);

        mButtonSelectEmployee = (Button) findViewById(R.id.buttonSelectEmployee);
        mButtonSelectEmployee.setOnClickListener(TDAllocationActivity.this);

        mParentLayout=(LinearLayout) findViewById(R.id.scrollLayout);

    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == mButtonSelectEmployee) {
            mParentEmpCode=Constants.employeeDetailObject.getEmpCode();
            mButtonSubmit.setVisibility(View.VISIBLE);
            PrepareSaudaAccessData(1,Constants.employeeDetailObject.getEmpCode());
        }
        if (clkdView == mButtonSubmit)
        {
            if(cd.isConnectingToInternet())
            {
                new GPSTracker(mContext);
                Boolean isInputTDValid =false;
                if(Constants.mTDAllocationList!=null && Constants.mTDAllocationList.size()>0 && mEditTextList.size()>0)
                {
                    isInputTDValid =true;
                }
                for(int count =0; count<Constants.mTDAllocationList.size();count++)
                {
                    String maxAllocatableTD=Constants.mTDAllocationList.get(count).getTDMax();
                    String currentProductGroup=Constants.mTDAllocationList.get(count).getProductFilterName();
                    String currentlyAllocatedValue=mEditTextList.get(count).getText().toString();
                    if(isInputTDValid)
                    {
                        if(currentlyAllocatedValue.matches(""))
                        {
                            currentlyAllocatedValue="0";
                        }
                        if(Utils.isNumeric(currentlyAllocatedValue))
                        {
                            //check if lower leaves of currently allocated employee has any allocation that is higher than the given allocation to this employee
                            Double maxTDOfLowerLeaves= Double.valueOf(Constants.mTDAllocationList.get(count).getTDAllocationLowerLimit());
                            if(Double.parseDouble(currentlyAllocatedValue)>=maxTDOfLowerLeaves)
                            {
                                //boss can allocate to its lower level even if his td allocation is zero
                                if(isBoss)
                                {
                                    Constants.mTDAllocationList.get(count).setTDAllocated(currentlyAllocatedValue);
                                }
                                //if not boss then check if allocated discount is exceeding max allowed discount or not
                                else if(Double.parseDouble(currentlyAllocatedValue)<=Double.parseDouble(maxAllocatableTD))
                                {
                                    Constants.mTDAllocationList.get(count).setTDAllocated(currentlyAllocatedValue);
                                }
                                else
                                {
                                    isInputTDValid =false;
                                    Toast.makeText(mContext, "Exceeded max discount for "+currentProductGroup, Toast.LENGTH_LONG).show();
                                }
                            }
                            else
                            {
                                isInputTDValid =false;
                                Toast.makeText(mContext, "Can not provide value lower than "+maxTDOfLowerLeaves+" for "+currentProductGroup, Toast.LENGTH_LONG).show();
                            }

                        }
                        else
                        {
                            isInputTDValid =false;
                            Toast.makeText(mContext, "Please provide proper input for "+currentProductGroup, Toast.LENGTH_LONG).show();
                        }
                    }

                }
                if(isInputTDValid)
                {
                    mButtonSubmit.setEnabled(false);
                    PrepareSaudaAccessData(3,"");
                }
            }
            else
            {
                Utils.showToast(mContext,"You need an active internet connection to allocate discount.");
            }

        }
    }

    public void PrepareSaudaAccessData(final int task,final String empcode) {
        mProgressDialogSaudaAllocation = new ProgressDialog(mContext);
        mProgressDialogSaudaAllocation.setCancelable(false);
        if(task==3){
            mProgressDialogSaudaAllocation.setMessage("Saving data.\nPlease wait..");
        }else if(task==4){
            mProgressDialogSaudaAllocation.setMessage("Downloading data.\nPlease wait..");
        }else{

            mProgressDialogSaudaAllocation.setMessage("Fetching data from database.\nPlease wait..");
        }
        mProgressDialogSaudaAllocation.show();
        new Thread() {
            public void run() {

                switch(task){

                    case 1:
                        mEmployeeMasterDetailsList=mAceDnsDatabase.GetEmployeeForTDAllocation(empcode);
                        currentParentEmployee=empcode;
                        mAllocation=mAceDnsDatabase.GetAllocationinHierarchy(empcode);
                        mOwnAllocation=mAceDnsDatabase.GetOwnAllocationinTDFHierarchy(empcode);
                        break;
                    case 2:
                        mAceDnsDatabase.GetProductGroupListWithTDForTDAllocation(mEmployeeMasterDetails.getEmpCode());
                        break;
                    case 3:
                        SaveDatatoDatabase();
                        break;
                    case 4:
                        if( cd.isConnectingToInternet())
                        {
                            //upload any unuploaded data
                            new TRANS_SubmitTDAllocation(mContext,false).execute();
                            //get latest allocations
                            new commonAsyncTaskMaster(mContext,"TD_allocation");
                        }

                        break;
                }

                Message msg = mHandlerSaudaAllocation.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerSaudaAllocation.sendMessage(msg);
            }
        }.start();
    }

    public void ShowEmployee()
    {
        if(mEmployeeMasterDetailsList.size()>0)
        {
            if(mEmployeeMasterDetailsList.size()==1)
            {
                mEmployeeMasterDetails=mEmployeeMasterDetailsList.get(0);
                mButtonSelectEmployee.setText(mEmployeeMasterDetails.getEmpName());
                mCurrentEmployeeName=mEmployeeMasterDetails.getEmpName();
                if(mAceDnsDatabase.GetEmployeeForTDAllocation(mEmployeeMasterDetails.getEmpCode()).size()<1)
                {
                    mType="ADD";
                    PrepareSaudaAccessData(2, "");
                }
                else
                {
                    ShowConditionofAllocation();
                }
            }
            else
            {
                ShowEmployeeDialog();
            }

        }
        else
        {
            Utils.showToast(mContext, "No employee found");
        }
    }

    public void ShowEmployeeDialog(){
        EmployeeAdapter adapter = new EmployeeAdapter(TDAllocationActivity.this,R.layout.customer_broker_list_child,mEmployeeMasterDetailsList);
        final Dialog dialogEmployeeList = new Dialog(TDAllocationActivity.this,R.style.PauseDialog);
        dialogEmployeeList.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogEmployeeList.setContentView(R.layout.select_from_list);
        dialogEmployeeList.setCancelable(false);
        TextView title = (TextView)dialogEmployeeList.findViewById(R.id.title);
        title.setText("Please select an employee");

        ListView list = (ListView) dialogEmployeeList.findViewById(R.id.list);
        list.setAdapter(adapter);
        list.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos,long arg3) {
                mEmployeeMasterDetails=mEmployeeMasterDetailsList.get(pos);
                mCurrentEmployeeName=mEmployeeMasterDetails.getEmpName();
                mButtonSelectEmployee.setText(mCurrentEmployeeName);
                dialogEmployeeList.cancel();
                if(mAceDnsDatabase.GetEmployeeForTDAllocation(mEmployeeMasterDetails.getEmpCode()).size()<1)
                {
                    mType="ADD";
                    PrepareSaudaAccessData(2, "");
                }
                else
                {
                    ShowConditionofAllocation();
                }
//					String allocation=mAceDnsDatabase.GetOwnAllocationinTDFHierarchy(mEmployeeMasterDetails.getEmpCode());
//					if(allocation.equalsIgnoreCase("0") || allocation.equalsIgnoreCase("") )
//					{
//						mType="ADD";
//						PrepareSaudaAccessData(2, "");
//					}
//					else
//					{
//						ShowConditionofAllocation();
//					}
            }
        });

        Button cancel = (Button) dialogEmployeeList.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        dialogEmployeeList.show();

    }

    public boolean SaveDatatoDatabase()
    {
        boolean isSuccess=false;
        try{
            String transactiotype="TDA";
            String timeStamp = "";
            timeStamp = Constants.dateString+ new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());


            modifyUpperLeaveEmployees();

            mAceDnsTransactionDatabase.INSERTtoTDAllocationLog(timeStamp,transactiotype);
            mAceDnsTransactionDatabase.insertToLocationTable(transactiotype, timeStamp);
            mAceDnsTransactionDatabase.UpdateTDAllocation();
            isSuccess=true;
        }catch(Exception ex){
            ex.printStackTrace();
        }
        return isSuccess;
    }

    private void modifyUpperLeaveEmployees()
    {
        ArrayList<TDAllocation> mTDAllocationListCopy=new ArrayList<>(Constants.mTDAllocationList);
        for (int count = 0; count < mTDAllocationListCopy.size(); count++)
        {
            TDAllocation tdAllocationObjectItem = Constants.mTDAllocationList.get(count);
            String currentProductGroupCode=tdAllocationObjectItem.getProductFilterCode();
            String tdAllocated=tdAllocationObjectItem.getTDAllocated();
            String tdPrevious=tdAllocationObjectItem.getTDAllocatedOld();
            String currentlyAllocatedToTheEmployee=tdAllocationObjectItem.getEmployeCode();

            if(Double.parseDouble(tdAllocated)>Double.parseDouble(tdPrevious))
            {
                currentlyAllocatedToTheEmployee =mAceDnsDatabase.getImmediateBossOfCurrentEmployee(currentlyAllocatedToTheEmployee);
                if(!currentlyAllocatedToTheEmployee.matches(""))
                {
                    tdPrevious = mAceDnsDatabase.GetTDOfEmployee(currentlyAllocatedToTheEmployee, currentProductGroupCode);
                    if(!Utils.isNumeric(tdPrevious))
                    {
                        tdPrevious="0";
                    }
                    while(Double.parseDouble(tdAllocated)>Double.parseDouble(tdPrevious))
                    {
                        TDAllocation obj = new TDAllocation();
                        obj.setProductFilterCode(currentProductGroupCode);
                        obj.setEmployeCode(currentlyAllocatedToTheEmployee);
                        obj.setTDAllocated(tdAllocated);
                        Constants.mTDAllocationList.add(obj);
                        currentlyAllocatedToTheEmployee =mAceDnsDatabase.getImmediateBossOfCurrentEmployee(currentlyAllocatedToTheEmployee);
                        if(!currentlyAllocatedToTheEmployee.matches(""))
                        {
                            tdPrevious = mAceDnsDatabase.GetTDOfEmployee(currentlyAllocatedToTheEmployee, currentProductGroupCode);
                            if(!Utils.isNumeric(tdPrevious))
                            {
                                tdPrevious="0";
                            }
                        }
                        else
                        {
                            break;
                        }

                    }
                }
            }

        }
    }

    public void ShowConditionofAllocation() {
        final Dialog dialgoCondition = new Dialog(TDAllocationActivity.this,R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_layout);
        TextView txtMsg = (TextView) dialgoCondition
                .findViewById(R.id.title);
        txtMsg.setText("Select an Option.");

        final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition
                .findViewById(R.id.radioSelect);
        ;

        radioSelectionGroup
                .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        RadioButton radioSelection = (RadioButton) dialgoCondition
                                .findViewById(checkedId);
                        if (radioSelection.getText().equals("Edit")) {
                            mType="EDIT";
                            PrepareSaudaAccessData(2, "");
                        } else {
                            mParentEmpCode=mEmployeeMasterDetails.getEmpCode();
                            mType="ADD";
                            PrepareSaudaAccessData(1,mEmployeeMasterDetails.getEmpCode());

                        }
                        dialgoCondition.cancel();
                    }
                });
        dialgoCondition.show();
    }

    public void DrawView()
    {
        mEditTextList = new ArrayList<EditText>();
        mParentLayout.removeAllViews();
        mButtonSubmit.setEnabled(true);
        LayoutParams childlayoutparam = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT,100f);

        LayoutParams prdGrpParam = new LayoutParams(0, LayoutParams.WRAP_CONTENT,35f);
        LayoutParams allocationParam = new LayoutParams(0, LayoutParams.WRAP_CONTENT,20f);
        LayoutParams allocationLimitParam = new LayoutParams(0, LayoutParams.WRAP_CONTENT,25f);

        String productgroupname		="";
        String maxTD				="";
        String allocatedTD				="";
        String allocatedTDLowerLimit				="";

        for(int count =0; count<Constants.mTDAllocationList.size();count++)
        {

            productgroupname=Constants.mTDAllocationList.get(count).getProductFilterName();
            maxTD=Constants.mTDAllocationList.get(count).getTDMax();
            allocatedTD=Constants.mTDAllocationList.get(count).getTDAllocatedOld();
            allocatedTDLowerLimit=Constants.mTDAllocationList.get(count).getTDAllocationLowerLimit();

            LinearLayout childlayout = new LinearLayout(this);
            childlayout.setPadding(4, 2, 0, 2);
            childlayout.setOrientation(LinearLayout.HORIZONTAL);
            childlayout.setLayoutParams(childlayoutparam);
            //childlayout.setWeightSum(3);
            childlayout.setBackgroundColor(Color.parseColor("#DCE8F6"));

            TextView textviewdisplayname = new TextView(this);
            textviewdisplayname.setText(productgroupname);
            textviewdisplayname.setTextColor(Color.parseColor("#003399"));
            textviewdisplayname.setLayoutParams(prdGrpParam);

            childlayout.addView(textviewdisplayname);

            EditText editText = new EditText(this);
            editText.setLayoutParams(allocationParam);
            editText.setTag(String.valueOf(count));
            editText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            editText.setImeOptions(EditorInfo.IME_ACTION_DONE);
            editText.setTypeface(null, Typeface.NORMAL);
            editText.setGravity(Gravity.RIGHT | Gravity.CENTER_VERTICAL);
            editText.setText(allocatedTD);
            mEditTextList.add(editText);
            childlayout.addView(editText);

            TextView textviewMax = new TextView(this);
            textviewMax.setTextColor(Color.parseColor("#336600"));
            if(isBoss)
            {
                textviewMax.setText("N.A.");
            }
            else
            {
                textviewMax.setText(maxTD+"  ");
            }

            textviewMax.setGravity(Gravity.CENTER);
            textviewMax.setLayoutParams(allocationLimitParam);
            childlayout.addView(textviewMax);

            TextView textviewMin = new TextView(this);
            textviewMin.setTextColor(Color.parseColor("#336600"));
            textviewMin.setText(allocatedTDLowerLimit+"  ");
            textviewMin.setGravity(Gravity.CENTER);
            textviewMin.setLayoutParams(allocationLimitParam);
            childlayout.addView(textviewMin);

            mParentLayout.addView(childlayout);
        }
    }
}
