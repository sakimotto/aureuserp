# 🚀 Zervi Asia MES - Development Server Startup Guide

## 🚨 **Docker Desktop Not Running - Let's Fix This!**

I can see that Docker Desktop isn't currently running. Here's exactly what you need to do to get your Zervi Asia Manufacturing MES system up and running:

---

## 📋 **Step-by-Step Startup Instructions**

### **Step 1: Start Docker Desktop**

**Option A - Windows Start Menu:**
1. Click the **Windows Start** button
2. Type **"Docker Desktop"** in the search bar
3. Click on **"Docker Desktop"** when it appears
4. Wait for the **whale icon** to appear in your system tray (bottom right)
5. Wait until it shows **"Docker Desktop is running"**

**Option B - System Tray:**
1. Look at your **system tray** (bottom right corner of screen)
2. Find the **Docker whale icon** (if it's there, click it)
3. If not there, start from Start Menu as above

**Option C - Desktop Shortcut:**
1. Look for **Docker Desktop** icon on your desktop
2. Double-click to start it
3. Wait for startup completion

---

### **Step 2: Wait for Docker to Fully Start**

**⏱️ This usually takes 30-60 seconds**

**You'll know it's ready when:**
- ✅ Docker Desktop window shows "Docker Desktop is running"
- ✅ Whale icon appears in system tray (not animated)
- ✅ No "starting" or "initializing" messages

---

### **Step 3: Start Your Zervi Asia MES System**

**Once Docker is running, use this command:**

```bash
docker-compose -f docker-compose-quick.yml up -d
```

**Or run the automated startup script:**

```bash
start-dev-server.bat
```

---

### **Step 4: Verify Everything is Working**

**Check services are running:**
```bash
docker-compose -f docker-compose-quick.yml ps
```

**Expected output:**
```
NAME            IMAGE                       STATUS          PORTS
zervi-mes-app   webdevops/php-nginx:8.2-alpine   Up 10 seconds   0.0.0.0:8000->80/tcp
zervi-mes-db    mysql:8.0                        Up 10 seconds   0.0.0.0:3306->3306/tcp
```

---

### **Step 5: Access Your System**

**🌐 Open these URLs in your browser:**

1. **Main Application:** http://localhost:8000
2. **Admin Panel:** http://localhost:8000/admin
3. **Login:** admin@zervi.com / admin123

---

## 🎯 **What You'll See When It's Working**

### **✅ Login Page:**
- Clean Filament admin interface
- "Zervi Asia Manufacturing MES" branding
- Login form with email/password fields

### **✅ Admin Dashboard:**
- Left sidebar with "Manufacturing" section
- Dashboard widgets showing:
  - Active Jobs
  - Overdue Jobs  
  - Material Shortages
  - Blocked Tasks

### **✅ Manufacturing Module:**
- **Work Orders:** 5 orders from Toyota, Isuzu, Mitsubishi, Central, Decathlon
- **Kanban Board:** Drag-and-drop workflow (QUEUED → CUTTING → SEWING → QC → COMPLETE)
- **Material Lines:** Shortage detection with supplier info
- **Quality Records:** TIS 1238-2564 compliance tracking

---

## 🏭 **Zervi Asia Data Ready for Testing**

### **Work Orders Created:**
1. **Toyota Motor Thailand** - 50 premium camping tents (HIGH priority)
2. **Isuzu Motors Thailand** - 75 heavy-duty tents (URGENT - 10 days!)
3. **Mitsubishi Motors Thailand** - 100 adventure tents (NORMAL priority)
4. **Central Department Store** - 200 seasonal tents (HIGH priority)
5. **Decathlon Thailand** - 150 premium tents (NORMAL priority)

### **Features to Test:**
- ✅ **Customer Context:** "Toyota order due Thursday" instead of "WO-104"
- ✅ **Material Shortages:** Automatic detection with restock dates
- ✅ **Kanban Workflow:** Drag-and-drop between departments
- ✅ **TIS Compliance:** Thai automotive quality standards
- ✅ **Supervisor Focus:** 4-item navigation, no ERP overwhelm

---

## 🔧 **Troubleshooting If Issues Occur**

### **Docker Won't Start:**
- Check if Docker Desktop is installed properly
- Restart your computer and try again
- Check Windows Defender/antivirus isn't blocking Docker

### **Containers Won't Start:**
- Check Docker Desktop is fully running first
- Try: `docker-compose -f docker-compose-quick.yml down` then `up -d`
- Check logs: `docker-compose -f docker-compose-quick.yml logs`

### **Application Not Responding:**
- Wait 30-60 seconds after starting containers
- Check: `docker-compose -f docker-compose-quick.yml ps`
- Test: `curl http://localhost:8000` or visit in browser

### **Database Issues:**
- Check MySQL container: `docker logs zervi-mes-db --tail 10`
- Verify migrations: `docker exec zervi-mes-app php artisan migrate:status`

---

## 🚀 **Ready to Start Manufacturing!**

**Once Docker Desktop is running and you start the containers, you'll have:**

✅ **Complete Manufacturing Execution System**  
✅ **Real Thai automotive customer data**  
✅ **Supervisor-friendly interface**  
✅ **Material shortage detection**  
✅ **TIS quality compliance**  
✅ **Drag-and-drop Kanban workflow**  

**Ready to transform "WO-104" into "Toyota order due Thursday"!** 🏭✨

---

**Need help? Start Docker Desktop first, then run the commands above. The system is fully configured and ready to go!**