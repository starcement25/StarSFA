package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.GroupLeaderFormList;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

public class TRANS_GroupLeader_Form_TransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_GroupLeader_Form_TransactionTask(Context context, ArrayList<Location> unUploadedTransaction)
    {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new  AceDnsTransactionDatabase(mContext);
        this.unUploadedTransaction=unUploadedTransaction;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute()
    {
        super.onPreExecute();
        //xmlData = prepareXMLData();
    }

    @Override
    protected String doInBackground(String... params)
    {
        //prepareXMLData();
        prepareXMLData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext))
        {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitGroupLeaderFormURL + "?nick_name=" + Constants.nickName;// + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GroupLeader_Form_TransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GroupLeader_Form_TransactionTask value: " +xmlData);
            POST_result=HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(uri,xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GroupLeader_Form_TransactionTask result: " +POST_result);
        return POST_result;

    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
        {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","location");
                }
        }

        mAceDnsTransactionDatabase.closeDatabase();
    }

    public void prepareXMLData()
    {

        JSONObject json = new JSONObject();
        try
        {

        xmlData = "";
        for(int ii=0;ii<unUploadedTransaction.size();ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);

            ArrayList<GroupLeaderFormList> unUploadedVisit = mAceDnsTransactionDatabase.getUnuploadedGroupLeaderForm(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedVisit.size();jj++)
            {
                GroupLeaderFormList detailsObj = unUploadedVisit.get(jj);


                    json.put("group_l_form_id", detailsObj.getGroup_l_form_id());
                    json.put("customer_name", detailsObj.getCustomer_name());
                json.put("mobile_no", detailsObj.getMobile_no());
                json.put("demo_achieved", detailsObj.getDemo_acheive());
                    json.put("group_l_photo", detailsObj.getGroup_l_photo());
                    json.put("group_l_photo_datetime", detailsObj.getGroup_l_photo_datetime());
                    json.put("group_l_photo_latt", detailsObj.getGroup_l_photo_latt());
                    json.put("group_l_photo_longi", detailsObj.getGroup_l_photo_longi());
                    json.put("tent_photo", detailsObj.getTent_photo());
                    json.put("tent_photo_datetime", detailsObj.getTent_photo_datetime());
                    json.put("tent_photo_latt", detailsObj.getTent_photo_latt());
                    json.put("tent_photo_longi", detailsObj.getTent_photo_longi());
                    json.put("demo_photo", detailsObj.getDemo_photo());
                    json.put("demo_photo_datetime", detailsObj.getDemo_photo_datetime());
                    json.put("demo_photo_latt", detailsObj.getDemo_photo_latt());
                    json.put("demo_photo_longi", detailsObj.getDemo_photo_longi());
                json.put("night_meet_photo", detailsObj.getNight_meet_photo());
                json.put("night_meet_photo_datetime", detailsObj.getNight_meet_photo_datetime());
                json.put("night_meet_photo_latt", detailsObj.getNight_meet_photo_latt());
                json.put("night_meet_photo_longi", detailsObj.getNight_meet_photo_longi());
                json.put("update_by", detailsObj.getUpdate_by());


            }


        }
        }
        catch (Exception e)
        {
            e.printStackTrace();
            System.out.println("JSON could not be made");
        }
        xmlData = json.toString();
        Log.d("jsonTent", ""+json.toString());
    }
}
