# 🚀 Zervi Asia MES - Development Server Startup Guide

## 🏭 **Ready to Spin Up Your Manufacturing System!**

Your Zervi Asia Manufacturing MES is fully configured and ready to go! Here's how to start the development server:

---

## 📋 **Quick Start (Recommended)**

### **Step 1: Start Docker Desktop**

**IMPORTANT:** Docker Desktop must be running first!

**How to start Docker Desktop:**
1. **Windows Start Menu:** Search "Docker Desktop" → Click to start
2. **Wait for:** Whale icon appears in system tray (bottom right)
3. **Wait for:** "Docker Desktop is running" message
4. **This takes:** 30-60 seconds to fully start

### **Step 2: Start Your Development Server**

**Option A - Automated Script (Recommended):**
```bash
start-dev-server.bat
```

**Option B - Manual Docker Commands:**
```bash
docker-compose -f docker-compose-quick.yml up -d
```

**Option C - Alternative Manual Method:**
```bash
start-dev-server-manual.bat
```

---

## ✅ **Verify System is Running**

### **Check Services:**
```bash
docker-compose -f docker-compose-quick.yml ps
```

**Expected Output:**
```
NAME            IMAGE                       STATUS          PORTS
zervi-mes-app   webdevops/php-nginx:8.2-alpine   Up 10 seconds   0.0.0.0:8000->80/tcp
zervi-mes-db    mysql:8.0                        Up 10 seconds   0.0.0.0:3306->3306/tcp
```

### **Test Application:**
```bash
curl http://localhost:8000
# OR visit in browser: http://localhost:8000
```

---

## 🌐 **Access Your Zervi Asia MES System**

### **Main Application:**
```
http://localhost:8000
```

### **Admin Panel:**
```
http://localhost:8000/admin
Login: admin@zervi.com / admin123
```

### **Manufacturing Module:**
```
Manufacturing → Work Orders / Kanban Board / Material Lines
```

---

## 🎯 **What You'll See (Zervi Asia Mock Data)**

### **Work Orders Created:**
1. **Toyota Motor Thailand** - 50 premium camping tents (HIGH priority)
2. **Isuzu Motors Thailand** - 75 heavy-duty tents (URGENT - 10 days!)
3. **Mitsubishi Motors Thailand** - 100 adventure tents (NORMAL priority)
4. **Central Department Store** - 200 seasonal tents (HIGH priority)
5. **Decathlon Thailand** - 150 premium tents (NORMAL priority)

### **Features to Test:**
- ✅ **Customer Context:** "Toyota order due Thursday" vs "WO-104"
- ✅ **Material Shortages:** Automatic detection with supplier info
- ✅ **Kanban Workflow:** Drag-and-drop between departments
- ✅ **TIS Compliance:** Thai automotive quality standards
- ✅ **Supervisor Focus:** 4-item navigation, no ERP overwhelm

---

## 🛠️ **Troubleshooting**

### **Docker Desktop Won't Start:**
1. **Check if installed:** Look for Docker Desktop in Start Menu
2. **Restart computer:** Sometimes helps with Docker issues
3. **Check Windows Defender:** Ensure Docker isn't blocked
4. **Reinstall if needed:** Download from docker.com

### **Containers Won't Start:**
1. **Wait longer:** Docker can take 1-2 minutes to fully start
2. **Check Docker status:** Ensure whale icon is in system tray
3. **Try manual commands:** Use Option B or C above
4. **Check logs:** `docker-compose -f docker-compose-quick.yml logs`

### **Application Not Responding:**
1. **Wait 30-60 seconds:** After starting containers
2. **Check service status:** `docker-compose -f docker-compose-quick.yml ps`
3. **Test connection:** `curl http://localhost:8000`
4. **Check logs:** `docker logs zervi-mes-app --tail 20`

---

## 📚 **Documentation Available**

### **Setup Guides:**
- `ADMIN_ACCESS_GUIDE.md` - Complete login and access guide
- `ZERVI_ASIA_SETUP_COMPLETE.md` - Mock company details
- `SYSTEM_STATUS_TROUBLESHOOTING.md` - Troubleshooting guide
- `DOCKER_STARTUP_GUIDE.md` - Detailed Docker instructions

### **Feature Documentation:**
- `plugins/ZerviManufacturing/README.md` - System overview
- `plugins/ZerviManufacturing/API_DOCUMENTATION.md` - API reference
- `plugins/ZerviManufacturing/TESTING_GUIDE.md` - Testing procedures

---

## 🎊 **Success Indicators**

**When everything is working, you'll see:**
- ✅ **Login page** loads at http://localhost:8000/admin
- ✅ **Manufacturing module** in left sidebar
- ✅ **Customer names** instead of cryptic codes
- ✅ **Color-coded priorities** (urgent=red, high=orange)
- ✅ **Drag-and-drop Kanban** board functional
- ✅ **Material shortage alerts** with supplier info

---

## 🏆 **Your Manufacturing Revolution Awaits!**

**Once Docker Desktop is running and you start the containers, you'll have:**

✅ **Complete Manufacturing Execution System**  
✅ **Real Thai automotive customer data**  
✅ **Supervisor-friendly interface**  
✅ **Material shortage detection**  
✅ **TIS quality compliance**  
✅ **Real-time Kanban workflow**  

**Ready to transform "WO-104" into "Toyota order due Thursday"!** 🏭✨

---

**Need help? Start Docker Desktop first, then run the startup script. The system is fully configured and ready to demonstrate!**