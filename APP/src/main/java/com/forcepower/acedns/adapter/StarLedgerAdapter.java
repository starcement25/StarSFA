package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.starSaathiLedger;

import java.util.List;


public class StarLedgerAdapter extends RecyclerView.Adapter<StarLedgerAdapter.MyViewHolder> {

    List<starSaathiLedger> starSaathiLedger;
    Context context;
    String Tag;

    public StarLedgerAdapter(List<starSaathiLedger> categoryList, Context context) {
        this.starSaathiLedger = categoryList;
        this.context = context;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int i) {
        View itemView;

            itemView = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.row_star_saathi_ledger, parent, false);


        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull final MyViewHolder holder, int position) {

        final starSaathiLedger starSaathiLedgerL = starSaathiLedger.get(position);
        holder.title.setText(starSaathiLedgerL.getVoucher_no());
        holder.ldcr.setText(starSaathiLedgerL.getAmount_cr());
        holder.lddr.setText(starSaathiLedgerL.getAmount_dr());
        holder.narration.setText(starSaathiLedgerL.getNarration());
        holder.voucher_date.setText(starSaathiLedgerL.getVoucher_date());

        if(position %2 == 0){
           // holder.ln.setBackgroundColor(ContextCompat.getColor(context,R.color.amdp_dark_gray));
        }


    }

    @Override
    public int getItemCount() {
        return starSaathiLedger.size();
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        TextView title,ldcr,lddr,narration,voucher_date;
        LinearLayout ln;

        public MyViewHolder(@NonNull View itemView) {
            super(itemView);

            title = itemView.findViewById(R.id.vno);
            ldcr = itemView.findViewById(R.id.ldcr);
            lddr = itemView.findViewById(R.id.lddr);
            narration = itemView.findViewById(R.id.narration);
            voucher_date = itemView.findViewById(R.id.voucher_date);
            ln = itemView.findViewById(R.id.ln);

        }
    }
}
