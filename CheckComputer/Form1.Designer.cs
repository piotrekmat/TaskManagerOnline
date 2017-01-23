namespace CheckComputer
{
    partial class Form1
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
            this.cb_Computer = new System.Windows.Forms.ComboBox();
            this.label1 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.tb_Cpu = new System.Windows.Forms.TextBox();
            this.tb_Ram = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.tb_Procesy = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.btn_details = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // cb_Computer
            // 
            this.cb_Computer.FormattingEnabled = true;
            this.cb_Computer.Location = new System.Drawing.Point(35, 38);
            this.cb_Computer.Name = "cb_Computer";
            this.cb_Computer.Size = new System.Drawing.Size(196, 21);
            this.cb_Computer.TabIndex = 1;
            this.cb_Computer.SelectionChangeCommitted += new System.EventHandler(this.cb_Computer_SelectionChangeCommitted);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(32, 22);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(92, 13);
            this.label1.TabIndex = 2;
            this.label1.Text = "Wybierz komputer";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(35, 75);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(29, 13);
            this.label2.TabIndex = 3;
            this.label2.Text = "CPU";
            // 
            // tb_Cpu
            // 
            this.tb_Cpu.Location = new System.Drawing.Point(38, 92);
            this.tb_Cpu.Name = "tb_Cpu";
            this.tb_Cpu.Size = new System.Drawing.Size(100, 20);
            this.tb_Cpu.TabIndex = 4;
            // 
            // tb_Ram
            // 
            this.tb_Ram.Location = new System.Drawing.Point(38, 140);
            this.tb_Ram.Name = "tb_Ram";
            this.tb_Ram.Size = new System.Drawing.Size(100, 20);
            this.tb_Ram.TabIndex = 6;
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(35, 123);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(31, 13);
            this.label3.TabIndex = 5;
            this.label3.Text = "RAM";
            // 
            // tb_Procesy
            // 
            this.tb_Procesy.Location = new System.Drawing.Point(38, 188);
            this.tb_Procesy.Name = "tb_Procesy";
            this.tb_Procesy.Size = new System.Drawing.Size(100, 20);
            this.tb_Procesy.TabIndex = 8;
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Location = new System.Drawing.Point(35, 171);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(45, 13);
            this.label4.TabIndex = 7;
            this.label4.Text = "Procesy";
            // 
            // btn_details
            // 
            this.btn_details.Location = new System.Drawing.Point(38, 228);
            this.btn_details.Name = "btn_details";
            this.btn_details.Size = new System.Drawing.Size(100, 23);
            this.btn_details.TabIndex = 9;
            this.btn_details.Text = "Pokaż szczegóły";
            this.btn_details.UseVisualStyleBackColor = true;
            this.btn_details.Click += new System.EventHandler(this.btn_details_Click);
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(252, 282);
            this.Controls.Add(this.btn_details);
            this.Controls.Add(this.tb_Procesy);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.tb_Ram);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.tb_Cpu);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.cb_Computer);
            this.Name = "Form1";
            this.Text = "Parametry komptera";
            this.Load += new System.EventHandler(this.Form1_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion
        private System.Windows.Forms.ComboBox cb_Computer;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox tb_Cpu;
        private System.Windows.Forms.TextBox tb_Ram;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.TextBox tb_Procesy;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Button btn_details;
    }
}

