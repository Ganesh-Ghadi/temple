import React from "react";
import { Button } from "@/components/ui/button";
import axios from "axios";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { ScrollArea } from "@/components/ui/scroll-area";

import { User } from "lucide-react";
import ColorDisplay from "./ColorDisplay";
import {
  IndianRupee,
  AlignStartVertical,
  AlignVerticalDistributeCenter,
  Droplet,
} from "lucide-react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useNavigate } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
const Homepage = () => {
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();

  const {
    data: DashboardData,
    isLoading: isDashboardDataLoading,
    isError: isDashboardDataError,
  } = useQuery({
    queryKey: ["dashboards"], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get("/api/dashboards", {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const {
    ProfileCount,
    ReceiptCount,
    ReceiptAmount,
    CancelledReceiptCount,
    PoojaDetails,
    HallBookingDetails,
    PoojaCount,
    HallBookingCount,
    SareeDetails,
    UparaneDetails,
  } = DashboardData || {};

  if (isDashboardDataError) {
    return <p>Error</p>;
  }
  return (
    <>
      <div className="w-full p-5">
        {/* <h1 className="text-2xl">Dashboard</h1> */}
        <Tabs defaultValue="overview" className="space-y-4">
          <TabsContent value="overview" className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Total Amount
                  </CardTitle>

                  <IndianRupee size={16} color="#716f6f" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{ReceiptAmount}</div>
                  <p className="text-xs text-muted-foreground">
                    {/* +20.1% from last month */}
                  </p>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Total Receipts
                  </CardTitle>

                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    className="h-4 w-4 text-muted-foreground"
                  >
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <path d="M2 10h20" />
                  </svg>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{ReceiptCount}</div>
                  <p className="text-xs text-muted-foreground">
                    {/* +180.1% from last month */}
                  </p>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Cancelled Receipts
                  </CardTitle>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    className="h-4 w-4 text-muted-foreground"
                  >
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                  </svg>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">
                    {CancelledReceiptCount}
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {/* +201 since last hour */}
                  </p>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Total Users
                  </CardTitle>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    className="h-4 w-4 text-muted-foreground"
                  >
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                  </svg>
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{ProfileCount}</div>
                  <p className="text-xs text-muted-foreground">
                    {/* +19% from last month */}
                  </p>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>

        <Tabs defaultValue="overview" className="space-y-4">
          <TabsContent value="overview" className="space-y-4">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
              <Card className="w-full">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Saree
                  </CardTitle>
                  <AlignStartVertical size={16} color="#716f6f" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">
                    {SareeDetails?.name || "N/A"}
                  </div>
                </CardContent>
              </Card>
              <Card className="w-full">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Uparane
                  </CardTitle>
                  <AlignVerticalDistributeCenter size={16} color="#716f6f" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">
                    {" "}
                    {UparaneDetails?.name || "N/A"}
                  </div>
                </CardContent>
              </Card>
              {/* <Card className="w-full">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    Today's Prasad
                  </CardTitle>
                  <Droplet size={16} color="#716f6f" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{ReceiptCount}</div>
                </CardContent>
              </Card> */}
            </div>
          </TabsContent>
        </Tabs>

        {/* pooja */}
        <div className="w-full flex items-center gap-4">
          <ScrollArea className="dark:bg-background md:w-[50%] h-[500px] my-5 px-6 py-5 border rounded-xl bg-white">
            <div className="flex justify-between">
              <h3 className="text-xl mb-5 font-bold">Today's Pooja </h3>
              <p className="text-lg font-semibold text-white bg-blue-600 rounded-full w-8 h-8 flex items-center justify-center">
                {PoojaCount}
              </p>
            </div>
            <div className="space-y-6">
              {PoojaDetails?.map((pooja) => (
                <div className="flex items-center">
                  <User size={24} />
                  <div className="ml-4 space-y-1">
                    <p className="text-sm font-medium leading-none">
                      {pooja.name}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {pooja.gotra}
                    </p>
                  </div>
                  <div className="ml-auto font-medium">{pooja.pooja_type}</div>
                </div>
              ))}
            </div>
          </ScrollArea>
          <ScrollArea className="dark:bg-background md:w-[50%] h-[500px] my-5 px-6 py-5 border rounded-xl bg-white">
            <div className="flex justify-between">
              <h3 className="text-xl mb-5 font-bold">Today's Hall Bookings</h3>
              <p className="text-lg font-semibold text-white bg-blue-600 rounded-full w-8 h-8 flex items-center justify-center">
                {HallBookingCount}
              </p>
            </div>
            <div className="space-y-6">
              {HallBookingDetails?.map((hall) => (
                <div className="flex items-center">
                  <User size={24} />
                  <div className="ml-4 space-y-1">
                    <p className="text-sm font-medium leading-none">
                      {hall.name}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {hall.hall_name}
                    </p>
                  </div>
                  <div className="ml-auto font-medium">
                    {hall.from_time} - {hall.to_time}
                  </div>
                </div>
              ))}
            </div>
          </ScrollArea>
        </div>
      </div>
    </>
  );
};

export default Homepage;
