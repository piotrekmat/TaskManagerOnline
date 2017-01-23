using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Linq;
using System.Windows.Forms;
namespace CheckComputer
{
    public partial class Form1 : Form
    {
        Controler controler;
        public Form1()
        {
            InitializeComponent();
            controler = new Controler(this);
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            controler.OnLoad();
        }

        public void SetCpu(string value)
        {
            tb_Cpu.Text = value;
        }

        public void SetRam(string value)
        {
            tb_Ram.Text = value;
        }

        public void SetProcess(string value)
        {
            tb_Procesy.Text = value;
        }

        public void AddToCombo(string value)
        {
            cb_Computer.Items.Add(value);
        }

        
        private void cb_Computer_SelectionChangeCommitted(object sender, EventArgs e)
        {
            controler.SetDataFromComputer(cb_Computer.SelectedItem.ToString());
        }
    }
}
