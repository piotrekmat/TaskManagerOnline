namespace CheckComputer
{
    partial class ComputerDetails
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.Windows.Forms.DataVisualization.Charting.ChartArea chartArea1 = new System.Windows.Forms.DataVisualization.Charting.ChartArea();
            System.Windows.Forms.DataVisualization.Charting.Legend legend1 = new System.Windows.Forms.DataVisualization.Charting.Legend();
            System.Windows.Forms.DataVisualization.Charting.Series series1 = new System.Windows.Forms.DataVisualization.Charting.Series();
            System.Windows.Forms.DataVisualization.Charting.ChartArea chartArea2 = new System.Windows.Forms.DataVisualization.Charting.ChartArea();
            System.Windows.Forms.DataVisualization.Charting.Legend legend2 = new System.Windows.Forms.DataVisualization.Charting.Legend();
            System.Windows.Forms.DataVisualization.Charting.Series series2 = new System.Windows.Forms.DataVisualization.Charting.Series();
            this.ch_cpu = new System.Windows.Forms.DataVisualization.Charting.Chart();
            this.ch_Ram = new System.Windows.Forms.DataVisualization.Charting.Chart();
            this.label1 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            ((System.ComponentModel.ISupportInitialize)(this.ch_cpu)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.ch_Ram)).BeginInit();
            this.SuspendLayout();
            // 
            // ch_cpu
            // 
            chartArea1.Name = "ChartArea1";
            this.ch_cpu.ChartAreas.Add(chartArea1);
            legend1.Name = "Legend1";
            this.ch_cpu.Legends.Add(legend1);
            this.ch_cpu.Location = new System.Drawing.Point(39, 39);
            this.ch_cpu.Name = "ch_cpu";
            series1.ChartArea = "ChartArea1";
            series1.ChartType = System.Windows.Forms.DataVisualization.Charting.SeriesChartType.Line;
            series1.Legend = "Legend1";
            series1.Name = "Punkty";
            this.ch_cpu.Series.Add(series1);
            this.ch_cpu.Size = new System.Drawing.Size(300, 168);
            this.ch_cpu.TabIndex = 0;
            this.ch_cpu.Text = "ch_cpu";
            // 
            // ch_Ram
            // 
            chartArea2.Name = "ChartArea1";
            this.ch_Ram.ChartAreas.Add(chartArea2);
            legend2.Name = "Legend1";
            this.ch_Ram.Legends.Add(legend2);
            this.ch_Ram.Location = new System.Drawing.Point(433, 39);
            this.ch_Ram.Name = "ch_Ram";
            series2.ChartArea = "ChartArea1";
            series2.ChartType = System.Windows.Forms.DataVisualization.Charting.SeriesChartType.Line;
            series2.Legend = "Legend1";
            series2.Name = "Punkty";
            this.ch_Ram.Series.Add(series2);
            this.ch_Ram.Size = new System.Drawing.Size(300, 168);
            this.ch_Ram.TabIndex = 1;
            this.ch_Ram.Text = "ch_Ram";
            this.ch_Ram.Click += new System.EventHandler(this.ch_Ram_Click);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(39, 20);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(29, 13);
            this.label1.TabIndex = 2;
            this.label1.Text = "CPU";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(430, 20);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(31, 13);
            this.label2.TabIndex = 3;
            this.label2.Text = "RAM";
            // 
            // ComputerDetails
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(776, 397);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.ch_Ram);
            this.Controls.Add(this.ch_cpu);
            this.Name = "ComputerDetails";
            this.Text = "ComputerDetails";
            ((System.ComponentModel.ISupportInitialize)(this.ch_cpu)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.ch_Ram)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.DataVisualization.Charting.Chart ch_cpu;
        private System.Windows.Forms.DataVisualization.Charting.Chart ch_Ram;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label label2;
    }
}