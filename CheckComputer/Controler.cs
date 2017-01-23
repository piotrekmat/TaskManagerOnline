using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CheckComputer
{
    public class Controler
    {
        Model model = new Model();
        Form1 view;
        ComputerDetails view1;
        List<ComputerInformation> computers;
        public Controler(Form1 view)
        {
            this.view = view;
        }
        public void AddViewToControler(ComputerDetails view1)
        {
            this.view1 = view1;
        }
        public void OnLoad()
        {

            computers=model.TakeDataAboutComputers();
            foreach(ComputerInformation computer in computers)
            {
                view.AddToCombo(computer.ComputerName);
            }
        }
        public void OnLoadDeatail()
        {
            ShowDeatils();
            int i = 1;
            foreach (string data in model.cpu)
            {
                view1.SetCpuChartValue(i,int.Parse(data));
                i++;
            }
            int j = 0;
            foreach(string data in model.ram)
            {
                view1.SetRamChartValue(j, int.Parse(data));
                j++;
            }
        }
        public void SetDataFromComputer()
        {
            string value = view.IdCompterCombo;
            ComputerInformation computer = (from rekord in computers where rekord.ComputerName == value select rekord).FirstOrDefault();
            if (computer != null)
            {
                view.SetCpu(computer.CpuAvg);
                view.SetRam(computer.RamAvg);
                view.SetProcess(computer.ProcessAvg);
            }
        }

        public void ShowDeatils()
        {
            string id = (from rekord in computers where rekord.ComputerName == view.IdCompterCombo select rekord.Id).FirstOrDefault();
            if(!String.IsNullOrEmpty(id)) model.TakeAllDataAbotComputer(id);

        }
    }


}
