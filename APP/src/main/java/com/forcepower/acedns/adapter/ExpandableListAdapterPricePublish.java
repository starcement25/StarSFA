
package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.BaseExpandableListAdapter;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static com.forcepower.acedns.constants.Constants.publishRateList;
import static com.forcepower.acedns.constants.Constants.submitEnabledFlag;

public class ExpandableListAdapterPricePublish extends BaseExpandableListAdapter
{
    AceDnsDatabase mAceDnsDatabase;
    private Context mContext;
    private List<String> listDataHeader; // header titles
    // child data in format of header title, child title
    private HashMap<String, List<commonDatabaseHelper>> listDataChild;

    public ExpandableListAdapterPricePublish(Context context, List<String> listDataHeader, HashMap<String, List<commonDatabaseHelper>> listChildData) {
        this.mContext = context;
        this.listDataHeader = listDataHeader;
        this.listDataChild = listChildData;
        mAceDnsDatabase = new AceDnsDatabase(mContext);

    }

    @Override
    public Object getChild(int groupPosition, int childPosititon) {
        return this.listDataChild.get(this.listDataHeader.get(groupPosition)).get(childPosititon);
    }

    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return childPosition;
    }

    @Override
    public View getChildView(int groupPosition, final int childPosition,
                             boolean isLastChild, View convertView, ViewGroup parent) {

        commonDatabaseHelper ChildTextList = (commonDatabaseHelper)getChild(groupPosition, childPosition);
        final ChildViewHolder childViewHolder;
        if (convertView == null)
        {
            childViewHolder = new ChildViewHolder();
            LayoutInflater infalInflater = (LayoutInflater) this.mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.publish_report_list_item_child, null);
            childViewHolder.textViewSku = convertView.findViewById(R.id.textViewSku);
            childViewHolder.textViewQty = convertView.findViewById(R.id.textViewQty);
            childViewHolder.textViewAmount = convertView.findViewById(R.id.textViewAmount);
            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }
        childViewHolder.textViewSku.setText(ChildTextList.getItem1());
        childViewHolder.textViewQty.setText(ChildTextList.getItem4());
        childViewHolder.textViewAmount.setText(ChildTextList.getItem5());

        return convertView;
    }

    @Override
    public int getChildrenCount(int groupPosition) {
        return this.listDataChild.get(this.listDataHeader.get(groupPosition)).size();
    }

    @Override
    public Object getGroup(int groupPosition) {
        return this.listDataHeader.get(groupPosition);
    }

    @Override
    public int getGroupCount() {
        return this.listDataHeader.size();
    }

    @Override
    public long getGroupId(int groupPosition) {
        return groupPosition;
    }
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent)
    {
        final GroupViewHolder childViewHolder;
        String currentGrp = (String) getGroup(groupPosition);

            childViewHolder = new GroupViewHolder();
            LayoutInflater infalInflater = (LayoutInflater) this.mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.activity_group_header_item_3, null);

            childViewHolder.invProCode =  convertView.findViewById(R.id.invProCode);
            childViewHolder.dateSpinner =  convertView.findViewById(R.id.dateSpinner);
            childViewHolder.radioPrev =  convertView.findViewById(R.id.radioPrev);
            childViewHolder.radioCurrent =  convertView.findViewById(R.id.radioCurrent);
            childViewHolder.txt_grpName =  convertView.findViewById(R.id.txt_grpName);
            childViewHolder.radioGrpPriceType =  convertView.findViewById(R.id.radioGrpPriceType);

            convertView.setTag(childViewHolder);

        String currentOrPrev="",date="";
        for(int x=0;x<publishRateList.size();x++)
        {
            commonDatabaseHelper item=publishRateList.get(x);
            String prodGrp = item.getItem2();
            if(currentGrp.equalsIgnoreCase(prodGrp))
            {
                currentOrPrev= publishRateList.get(x).getItem6();
                date=publishRateList.get(x).getItem7();
                break;
            }
        }
        childViewHolder.dateSpinner.setVisibility(View.VISIBLE);

        if(currentOrPrev.equalsIgnoreCase("previous"))
        {
            childViewHolder.radioPrev.setChecked(true);
            childViewHolder.radioCurrent.setChecked(false);
        }
        else
        {
            if(currentOrPrev.equalsIgnoreCase("current"))
            {
                childViewHolder.radioPrev.setChecked(false);
                childViewHolder.radioCurrent.setChecked(true);
            }
            else
            {
                childViewHolder.radioPrev.setChecked(false);
                childViewHolder.radioCurrent.setChecked(false);
            }

        }

        final ArrayList<String> spinnerArray= mAceDnsDatabase.getPreviousRateOfChosenProductGroup(currentGrp);
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, R.layout.spinner_layout, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        childViewHolder.dateSpinner.setAdapter(spinnerArrayAdapter);
        if(date.matches(""))
        {

            if (!spinnerArray.isEmpty())
            {
                childViewHolder.dateSpinner.setSelection(0);
//                for(int x=0;x<publishRateList.size();x++)
//                {
//                    commonDatabaseHelper item=publishRateList.get(x);
//                    String prodGrp = item.getItem2();
//                    if(currentGrp.equalsIgnoreCase(prodGrp))
//                    {
//                        publishRateList.get(x).setItem7(spinnerArray.get(0));//date
//                    }
//                }
            }

        }
        else
        {
            int pos=0;
            for(int ii=0;ii<spinnerArray.size();ii++)
            {
                if(date.matches(spinnerArray.get(ii)))
                {
                    pos=ii;
                    break;
                }
            }
            childViewHolder.dateSpinner.setSelection(pos);
        }
//        if(childViewHolder.invProCode.getText().toString().equalsIgnoreCase(""))
//        {
            childViewHolder.radioGrpPriceType.setOnCheckedChangeListener(new GenericItemCheckedListener(groupPosition));
            childViewHolder.dateSpinner.setOnItemSelectedListener(new GenericItemSelectListener(groupPosition));
            childViewHolder.invProCode.setText("data set");
//        }

//        TextView txt_packSize =  convertView.findViewById(R.id.txt_packSize);
//        txt_packSize.setTypeface(null, Typeface.BOLD);
        childViewHolder.txt_grpName.setTypeface(null, Typeface.BOLD);
        childViewHolder.txt_grpName.setText("Group: "+currentGrp);
//        txt_packSize.setText("Pack Size: "+headerTitlesplitted[1]);
        return convertView;
    }
    @Override
    public boolean hasStableIds() {
        return false;
    }

    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return false;
    }
    public class ChildViewHolder
    {
        TextView  textViewSku, textViewQty, textViewAmount;
    }

    public class GroupViewHolder
    {
        Spinner dateSpinner;
        RadioGroup radioGrpPriceType;
        TextView  txt_grpName,invProCode;
        RadioButton radioPrev,radioCurrent ;
    }

    private class GenericItemCheckedListener implements RadioGroup.OnCheckedChangeListener
    {
        private int pos;

        private GenericItemCheckedListener(int pos) {
            this.pos = pos;
        }


        @Override
        public void onCheckedChanged(RadioGroup radioGroup, int checkedId)
        {
            int selectedId = radioGroup .getCheckedRadioButtonId();

            // find the radio button by returned id
            RadioButton radioButton = radioGroup.findViewById(selectedId);
            String currentGrp= listDataHeader.get(pos);
            String ValueType=radioButton.getText().toString();//current or previous
//            Utils.showToast(_context,currentGrp+"--"+ValueType);
            for(int x=0;x<publishRateList.size();x++)
            {
                commonDatabaseHelper item=publishRateList.get(x);
                String prodGrp = item.getItem2();
                if(currentGrp.equalsIgnoreCase(prodGrp))
                {
                    publishRateList.get(x).setItem6(ValueType);
                }
            }
            submitEnabledFlag=true;

        }
    }

    private class GenericItemSelectListener implements AdapterView.OnItemSelectedListener
    {
        private int pos;

        private GenericItemSelectListener(int pos)
        {
            this.pos = pos;
        }
        @Override
        public void onItemSelected(AdapterView<?> adapterView, View view, int pos, long l)
        {
            String dateChosen="",currentGrp="";
            try
            {
                 dateChosen=adapterView.getItemAtPosition(pos).toString();
                 currentGrp= listDataHeader.get(this.pos);

                boolean isDataChanged=false;
                for (Map.Entry mapElement : listDataChild.entrySet())
                {
                    String key = (String) mapElement.getKey();
//                    List<commonDatabaseHelper> val= (List<commonDatabaseHelper>) mapElement.getValue();
//                    List<commonDatabaseHelper> val= listDataChild.get(key);
                    if(currentGrp.equalsIgnoreCase(key))
                    {
                        for(int y=0;y<listDataChild.get(key).size();y++)
                        {
                            String date=listDataChild.get(key).get(y).getItem7();
                            if(!date.equalsIgnoreCase(dateChosen))
                            {
                                String currentProd=listDataChild.get(key).get(y).getItem0();
                                String currentPublishRateChanged=mAceDnsDatabase.getRelaseRateFromProdCodeDate(currentProd,dateChosen);
                                listDataChild.get(key).get(y).setItem7(dateChosen);
                                listDataChild.get(key).get(y).setItem5(currentPublishRateChanged);
                                isDataChanged=true;
                            }

                        }
                    }
//                    listDataChild.put(key,val);

                }
                if(isDataChanged)
                {
                    for(int x=0;x<publishRateList.size();x++)
                    {
                        commonDatabaseHelper item=publishRateList.get(x);
                        String prodGrp = item.getItem2();
                        if(currentGrp.equalsIgnoreCase(prodGrp))
                        {
                            publishRateList.get(x).setItem7(dateChosen);//date
                        }
                    }
                    notifyDataSetChanged();
                }

            }
            catch (Exception e)
            {
                e.printStackTrace();
            }


//            for(int x=0;x<publishRateList.size();x++)
//            {
//                commonDatabaseHelper item=publishRateList.get(x);
//                String prodGrp = item.getItem2();
//                if(currentGrp.equalsIgnoreCase(prodGrp))
//                {
//                    currentOrPrev= publishRateList.get(x).getItem6();
//                    break;
//                }
//            }

//                listDataChild=new HashMap<>();
//                for (Map.Entry mapElement : listDataChild.entrySet())
//                {
//                    String key = (String) mapElement.getKey();
////                    List<commonDatabaseHelper> val= (List<commonDatabaseHelper>) mapElement.getValue();
////                    List<commonDatabaseHelper> val= listDataChild.get(key);
//                    if(currentGrp.equalsIgnoreCase(key))
//                    {
//                        for(int y=0;y<listDataChild.get(key).size();y++)
//                        {
//                            String currentProd=listDataChild.get(key).get(y).getItem0();
//                            String currentPublishRateChanged=mAceDnsDatabase.getRelaseRateFromProdCodeDate(currentProd,dateChosen);
//                            listDataChild.get(key).get(y).setItem5(currentPublishRateChanged);
//                        }
//                    }
////                    listDataChild.put(key,val);
//
//                }
//                notifyDataSetChanged();
//                new ExpandableListAdapterPricePublish(mContext, listDataHeader, listDataChild);
//                ExpandableListAdapterPricePublish ReportAdapterObject = new ExpandableListAdapterPricePublish(mContext, listDataHeader, listDataChild);
//                expListView.setAdapter(ReportAdapterObject);
//                for (int i = 0; i < ReportAdapterObject.getGroupCount(); i++)
//                expListView.expandGroup(i);



        }

        @Override
        public void onNothingSelected(AdapterView<?> adapterView) {

        }
    }
}