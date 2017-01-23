using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace CheckComputer
{
    public partial class ComputerDetails : Form
    {
        Controler controler;
        public ComputerDetails(Controler controler)
        {
            InitializeComponent();
            this.controler = controler;
            controler.AddViewToControler(this);
            controler.OnLoadDeatail();
            
        }
        public void SetCpuChartValue(int x, int y)
        {
            ch_cpu.Series["Punkty"].Points.AddXY(x, y);
        }
        public void SetRamChartValue(int x, int y)
        {
            ch_Ram.Series["Punkty"].Points.AddXY(x, y);
        }

        private void ch_Ram_Click(object sender, EventArgs e)
        {

        }
    }
}
