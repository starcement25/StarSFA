package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;

public class ProductivityAdapter extends RecyclerView.Adapter<ProductivityAdapter.ViewHolder> {

    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;

    public ProductivityAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
        this.context = context;
        activity = (Activity) context;
        priceListOilGrp = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();
    }


    @Override
    public ProductivityAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(resourceId, parent, false);
        ViewHolder viewHolder = new ViewHolder(v);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        holder.itemView.setTag(priceListOilGrp.get(position));
        String desc = priceListOilGrp.get(position).getItem0();

        holder.firstValueTV.setText(Html.fromHtml(desc));
        holder.secondValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem1()));
        holder.thirdValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem2()));
        holder.forthValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem3()));
        holder.fifthValueTV.setText(Html.fromHtml(defaultFormatWithComma.format(Double.parseDouble(priceListOilGrp.get(position).getItem4()))));
        holder.sixthValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem5()));
        holder.seventhValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem6()));
        holder.eighthValueTV.setText(Html.fromHtml(defaultFormatWithComma.format(Double.parseDouble(priceListOilGrp.get(position).getItem7()))));
        holder.ninthValueTV.setText(Html.fromHtml(priceListOilGrp.get(position).getItem8()));
        holder.tenthValueTV.setText(Html.fromHtml(defaultFormatWithComma.format(Double.parseDouble(priceListOilGrp.get(position).getItem9()))));
    }


    @Override
    public int getItemCount() {
        return sizeOfList;
    }
    public class ViewHolder extends RecyclerView.ViewHolder{

        public TextView firstValueTV,secondValueTV,thirdValueTV,forthValueTV,fifthValueTV,sixthValueTV,seventhValueTV,eighthValueTV,ninthValueTV,tenthValueTV, invisibleTVProdCode;

        public ViewHolder(View itemView) {
            super(itemView);

            firstValueTV = (TextView) itemView.findViewById(R.id.firstValueTV);
            secondValueTV = (TextView) itemView.findViewById(R.id.secondValueTV);
            thirdValueTV = (TextView) itemView.findViewById(R.id.thirdValueTV);
            forthValueTV = (TextView) itemView.findViewById(R.id.forthValueTV);
            fifthValueTV = (TextView) itemView.findViewById(R.id.fifthValueTV);
            sixthValueTV = (TextView) itemView.findViewById(R.id.sixthValueTV);
            seventhValueTV = (TextView) itemView.findViewById(R.id.seventhValueTV);
            eighthValueTV = (TextView) itemView.findViewById(R.id.eighthValueTV);
            ninthValueTV = (TextView) itemView.findViewById(R.id.ninthValueTV);
            tenthValueTV = (TextView) itemView.findViewById(R.id.tenthValueTV);
            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {


                }
            });

        }
    }

}